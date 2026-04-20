<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issuance;
use App\Models\Requisition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssuanceController extends Controller
{
    public function index()
    {
        $issuances = Issuance::with(['requisition','issuer','receiver'])->latest()->paginate(10);
        return view('issuances.index', compact('issuances'));
    }

    public function create()
    {
        return view('issuances.create', [
            'requisitions' => Requisition::where('status', 'approved')->orderByDesc('id')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'requisition_id' => ['required','exists:requisitions,id'],
            'received_by' => ['required','exists:users,id'],
            'remarks' => ['nullable','string'],
        ]);

        $requisition = Requisition::findOrFail($data['requisition_id']);
        if ($requisition->status !== 'approved') {
            return back()->withErrors(['requisition_id' => 'Only approved requisitions can be issued.'])->withInput();
        }

        Issuance::create([
            'requisition_id' => $requisition->id,
            'issued_by' => Auth::id(),
            'received_by' => $data['received_by'],
            'issued_at' => now(),
            'status' => 'issued',
            'remarks' => $data['remarks'] ?? null,
        ]);

        $requisition->update(['status' => 'issued']);

        return redirect()->route('issuances.index')->with('success', 'Issuance recorded successfully.');
    }

    public function returnItem(Issuance $issuance)
    {
        $issuance->update(['status' => 'returned']);
        return redirect()->route('issuances.index')->with('success', 'Item marked as returned.');
    }
}
