<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Item;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Notifications\RequisitionStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequisitionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $user = Auth::user();

        $requisitions = Requisition::with(['department','user','items.item'])
            ->when($user->isRequestor(), fn ($query) => $query->where('user_id', $user->id))
            ->when($user->isApprover(), function ($query) use ($user) {
                if ($user->isDeanApprover()) {
                    $query->where('status', 'pending_college_dean');
                }
                if ($user->isExecutiveApprover()) {
                    $query->where('status', 'pending_executive_director');
                }
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('requisition_no', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('items.item', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('requisitions.index', compact('requisitions', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $departments = Auth::user()->isAdmin()
            ? Department::orderBy('name')->get()
            : Department::whereKey(Auth::user()->department_id)->get();

        $items = Item::with('acquisitions')->where('is_active', true)
            ->where('item_type', 'OPEX')
            ->where('availability_status', '!=', 'Out of Stock')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->latest_unit_cost = (float) optional($item->acquisitions->sortByDesc('acquisition_date')->first())->unit_cost;
                return $item;
            });

        return view('requisitions.create', [
            'departments' => $departments,
            'items' => $items,
            'selectedItemId' => $request->integer('item_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'branch' => ['required','string','max:255'],
            'charge_to_budget_item' => ['required','string','max:255'],
            'csf_no' => ['nullable','string','max:255'],
            'purpose' => ['nullable','string'],
            'requested_by_name' => ['required','string','max:255'],
            'checked_by_name' => ['nullable','string','max:255'],
            'approved_by_name' => ['nullable','string','max:255'],
            'items' => ['required','array','min:1'],
            'items.*.item_id' => ['required','exists:items,id'],
            'items.*.quantity_requested' => ['required','integer','min:1'],
            'items.*.remarks' => ['nullable','string','max:255'],
        ]);

        if (!Auth::user()->isAdmin() && (int) $data['department_id'] !== (int) Auth::user()->department_id) {
            return back()->withErrors(['department_id' => 'You can only request for your assigned department.'])->withInput();
        }

        DB::transaction(function () use ($data) {
            $requisition = Requisition::create([
                'requisition_no' => 'REQ-' . now()->format('YmdHis'),
                'user_id' => Auth::id(),
                'department_id' => $data['department_id'],
                'branch' => $data['branch'],
                'charge_to_budget_item' => $data['charge_to_budget_item'],
                'csf_no' => $data['csf_no'] ?? null,
                'requested_by_name' => $data['requested_by_name'],
                'checked_by_name' => $data['checked_by_name'] ?? null,
                'approved_by_name' => $data['approved_by_name'] ?? null,
                'status' => 'pending_asset_management',
                'purpose' => $data['purpose'] ?? null,
                'requested_at' => now(),
            ]);

            foreach ($data['items'] as $row) {
                $item = Item::findOrFail($row['item_id']);
                if ($item->item_type !== 'OPEX') {
                    throw ValidationException::withMessages([
                        'items' => 'Only OPEX items can be requested through the requisition form.',
                    ]);
                }

                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_id' => $item->id,
                    'quantity_requested' => (int) $row['quantity_requested'],
                    'remarks' => $row['remarks'] ?? null,
                ]);
            }

            foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
                $admin->notify(new RequisitionStatusNotification(
                    $requisition->fresh('department', 'user'),
                    'New requisition awaiting Asset Management review',
                    'A new charge slip request was submitted and is now waiting for Asset Management validation.'
                ));
            }
        });

        return redirect()->route('requisitions.index')->with('success', 'Charge slip requisition submitted successfully.');
    }

    public function show(Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $requisition->load(['department','user','items.item','assetReviewer','deanApprover','executiveApprover']);
        return view('requisitions.show', compact('requisition'));
    }

    public function approve(Request $request, Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $user = Auth::user();

        DB::transaction(function () use ($request, $requisition, $user) {
            $requisition->load('items.item');

            if ($user->isAdmin() && $requisition->isAwaitingAssetManagement()) {
                $validated = $request->validate([
                    'items' => ['required','array','min:1'],
                    'items.*.id' => ['required','exists:requisition_items,id'],
                    'items.*.quantity_approved' => ['required','integer','min:0'],
                    'items.*.remarks' => ['nullable','string','max:255'],
                ]);

                $hasApprovedQty = false;
                foreach ($validated['items'] as $row) {
                    $reqItem = $requisition->items->firstWhere('id', (int) $row['id']);
                    if (!$reqItem) {
                        continue;
                    }
                    $approvedQty = (int) $row['quantity_approved'];
                    if ($approvedQty > $reqItem->item->quantity) {
                        throw ValidationException::withMessages([
                            'items' => 'Approved quantity for '.$reqItem->item->name.' exceeds available stock.',
                        ]);
                    }
                    if ($approvedQty > $reqItem->quantity_requested) {
                        throw ValidationException::withMessages([
                            'items' => 'Approved quantity cannot be greater than requested quantity.',
                        ]);
                    }
                    $reqItem->update([
                        'quantity_approved' => $approvedQty,
                        'remarks' => $row['remarks'] ?? $reqItem->remarks,
                    ]);
                    $hasApprovedQty = $hasApprovedQty || $approvedQty > 0;
                }

                if (!$hasApprovedQty) {
                    throw ValidationException::withMessages([
                        'items' => 'At least one item must have an approved quantity greater than zero.',
                    ]);
                }

                $requisition->update([
                    'status' => 'pending_college_dean',
                    'asset_reviewed_by' => $user->id,
                    'asset_reviewed_at' => now(),
                ]);

                foreach (\App\Models\User::where('role', 'approver')->where('approver_type', 'dean')->get() as $dean) {
                    $dean->notify(new RequisitionStatusNotification(
                        $requisition->fresh('department', 'user'),
                        'Requisition awaiting College Dean approval',
                        'Asset Management has reviewed the requisition and forwarded it to the College Dean.'
                    ));
                }
                return;
            }

            if ($user->isDeanApprover() && $requisition->isAwaitingCollegeDean()) {
                $requisition->update([
                    'status' => 'pending_executive_director',
                    'dean_approved_by' => $user->id,
                    'dean_approved_at' => now(),
                ]);

                foreach (\App\Models\User::where('role', 'approver')->where('approver_type', 'executive')->get() as $executive) {
                    $executive->notify(new RequisitionStatusNotification(
                        $requisition->fresh('department', 'user'),
                        'Requisition awaiting Executive Director approval',
                        'The College Dean has approved the requisition and it now needs Executive Director approval.'
                    ));
                }
                return;
            }

            if ($user->isExecutiveApprover() && $requisition->isAwaitingExecutiveDirector()) {
                foreach ($requisition->items as $reqItem) {
                    $approvedQty = (int) ($reqItem->quantity_approved ?? 0);
                    if ($approvedQty > 0) {
                        $reqItem->item->decrement('quantity', $approvedQty);
                    }
                }

                $isPartial = $requisition->items->contains(fn ($item) => (int) ($item->quantity_approved ?? 0) < (int) $item->quantity_requested);
                $requisition->update([
                    'status' => $isPartial ? 'partially_approved' : 'approved',
                    'executive_approved_by' => $user->id,
                    'executive_approved_at' => now(),
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'finalized_at' => now(),
                ]);

                $requisition->user?->notify(new RequisitionStatusNotification(
                    $requisition->fresh('department', 'user'),
                    'Your requisition has been finalized',
                    $isPartial
                        ? 'Your requisition was partially approved based on available stock.'
                        : 'Your requisition was fully approved and finalized.'
                ));
                return;
            }

            abort(403, 'You are not allowed to approve this requisition at its current stage.');
        });

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition updated successfully.');
    }

    public function reject(Request $request, Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $request->validate(['reason' => ['required','string']]);
        $requisition->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'finalized_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        $requisition->user?->notify(new RequisitionStatusNotification(
            $requisition->fresh('department', 'user'),
            'Your requisition has been rejected',
            'Your requisition was rejected. Please review the rejection reason in the system.'
        ));

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition rejected successfully.');
    }

    private function authorizeAccess(Requisition $requisition): void
    {
        $user = Auth::user();
        if ($user->isRequestor() && (int) $requisition->user_id !== (int) $user->id) {
            abort(403);
        }
    }
}
