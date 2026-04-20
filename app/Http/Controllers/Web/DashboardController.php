<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acquisition;
use App\Models\Allocation;
use App\Models\Issuance;
use App\Models\Item;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockItems = Item::where('item_type', 'OPEX')
            ->whereColumn('quantity', '<=', 'low_stock_threshold')
            ->orderBy('quantity')
            ->get();

        $capexCount = Item::where('item_type', 'CAPEX')->count();
        $opexCount = Item::where('item_type', 'OPEX')->count();
        $pending = Requisition::whereIn('status', ['pending_asset_management', 'pending_college_dean', 'pending_executive_director'])->count();
        $lowStock = $lowStockItems->count();

        $allocationByDepartment = Allocation::query()
            ->join('departments', 'allocations.department_id', '=', 'departments.id')
            ->select(
                'departments.name',
                DB::raw("SUM(CASE WHEN allocations.item_type = 'CAPEX' THEN allocations.max_quantity ELSE 0 END) as capex"),
                DB::raw("SUM(CASE WHEN allocations.item_type = 'OPEX' THEN allocations.max_quantity ELSE 0 END) as opex")
            )
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('departments.name')
            ->get();

        $requisitionTrend = Requisition::selectRaw("strftime('%m', requested_at) as month_num")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('requested_at')
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();

        $categoryDistribution = Item::query()
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->selectRaw("COALESCE(item_categories.name, 'Uncategorized') as category_name")
            ->selectRaw('COUNT(items.id) as total')
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        $recentRequisitions = Requisition::with(['department','user','items.item'])->latest()->take(5)->get();
        $recentAcquisitions = Acquisition::with(['supplier','item'])->latest()->take(5)->get();
        $issuedAssets = Issuance::where('status', 'issued')->count();
        $approvedRequisitions = Requisition::whereIn('status', ['approved', 'partially_approved'])->count();
        $forecastReadyItems = RequisitionItem::whereNotNull('quantity_approved')->distinct('item_id')->count('item_id');

        return view('dashboard.index', compact(
            'capexCount',
            'opexCount',
            'pending',
            'lowStock',
            'lowStockItems',
            'allocationByDepartment',
            'requisitionTrend',
            'categoryDistribution',
            'recentRequisitions',
            'recentAcquisitions',
            'issuedAssets',
            'approvedRequisitions',
            'forecastReadyItems'
        ));
    }
}
