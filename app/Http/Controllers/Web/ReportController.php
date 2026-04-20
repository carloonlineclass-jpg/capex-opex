<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acquisition;
use App\Models\Department;
use App\Models\Issuance;
use App\Models\Item;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $inventoryByType = Item::select('item_type', DB::raw('COUNT(*) as total_items'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('item_type')
            ->orderBy('item_type')
            ->get();

        $lowStockItems = Item::with('category')
            ->where('item_type', 'OPEX')
            ->whereColumn('quantity', '<=', 'low_stock_threshold')
            ->orderBy('quantity')
            ->take(10)
            ->get();

        $departmentRequests = Department::query()
            ->leftJoin('requisitions', 'departments.id', '=', 'requisitions.department_id')
            ->select('departments.name', DB::raw('COUNT(requisitions.id) as total_requests'))
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total_requests')
            ->get();

        $requestStatus = Requisition::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $monthlyAcquisitions = Acquisition::selectRaw("strftime('%m', acquisition_date) as month_num")
            ->selectRaw('SUM(quantity * unit_cost) as total_amount')
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();

        $issuanceStatus = Issuance::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $forecastItems = RequisitionItem::with(['item','requisition'])
            ->whereHas('item', fn ($query) => $query->where('item_type', 'OPEX'))
            ->whereHas('requisition', fn ($query) => $query->whereIn('status', ['approved', 'partially_approved']))
            ->get()
            ->groupBy('item_id')
            ->map(function ($rows) {
                $item = $rows->first()?->item;
                $approvedQuantities = $rows->sortBy(fn ($row) => optional($row->requisition->finalized_at)->timestamp ?? 0)
                    ->pluck('quantity_approved')
                    ->filter(fn ($qty) => $qty !== null)
                    ->map(fn ($qty) => (int) $qty)
                    ->values();

                if (!$item || $approvedQuantities->isEmpty()) {
                    return null;
                }

                $recent = $approvedQuantities->take(-3);
                $forecast = (int) ceil($recent->avg());

                return [
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'current_stock' => $item->quantity,
                    'forecast_next_term' => $forecast,
                    'basis' => $recent->implode(', '),
                ];
            })
            ->filter()
            ->values();

        $assetLocationReport = Item::with('category')
            ->where('item_type', 'CAPEX')
            ->orderBy('room_assigned')
            ->orderBy('name')
            ->get();

        $approvalTracking = Requisition::with(['department', 'user'])
            ->latest('requested_at')
            ->take(10)
            ->get();

        $totals = [
            'assets' => Item::where('item_type', 'CAPEX')->count(),
            'consumables' => Item::where('item_type', 'OPEX')->count(),
            'requisitions' => Requisition::count(),
            'issued' => Issuance::where('status', 'issued')->count(),
            'acquisition_cost' => (float) Acquisition::sum(DB::raw('quantity * unit_cost')),
        ];

        return view('reports.index', compact(
            'inventoryByType',
            'lowStockItems',
            'departmentRequests',
            'requestStatus',
            'monthlyAcquisitions',
            'issuanceStatus',
            'totals',
            'forecastItems',
            'assetLocationReport',
            'approvalTracking'
        ));
    }
}
