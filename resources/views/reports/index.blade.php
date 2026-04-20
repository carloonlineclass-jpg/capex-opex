@extends('layouts.admin', ['title' => 'Reports'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">Detailed Reports</h2>
        <div class="module-note">Inventory, requisition, issuance, and acquisition analytics</div>
    </div>
</div>

<div class="stat-grid">
    <div class="report-stat"><div class="tiny-2">CAPEX Assets</div><div class="stat-value" style="font-size:28px">{{ $totals['assets'] }}</div></div>
    <div class="report-stat"><div class="tiny-2">OPEX Consumables</div><div class="stat-value" style="font-size:28px">{{ $totals['consumables'] }}</div></div>
    <div class="report-stat"><div class="tiny-2">Total Requisitions</div><div class="stat-value" style="font-size:28px">{{ $totals['requisitions'] }}</div></div>
    <div class="report-stat"><div class="tiny-2">Issued Records</div><div class="stat-value" style="font-size:28px">{{ $totals['issued'] }}</div></div>
</div>

<div class="report-grid">
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-boxes"></i> Inventory by Type</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="inventorySummaryChart"></canvas></div></div>
    </div>
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-pie-chart"></i> Requisition Status Mix</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="requestStatusChart"></canvas></div></div>
    </div>
</div>

<div class="report-grid">
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-building"></i> Requests by Department</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="departmentRequestChart"></canvas></div></div>
    </div>
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-currency-dollar"></i> Monthly Acquisition Cost</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="monthlyAcquisitionChart"></canvas></div></div>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Low Stock Report</h2>
            <div class="module-note">Consumables below or equal to their stock threshold</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item Code</th><th>Name</th><th>Category</th><th>Quantity</th><th>Threshold</th></tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                <tr>
                    <td data-label="Item Code">{{ $item->item_code }}</td>
                    <td data-label="Name">{{ $item->name }}</td>
                    <td data-label="Category">{{ $item->category->name ?? 'Office Supplies' }}</td>
                    <td data-label="Quantity"><span class="status low">{{ $item->quantity }}</span></td>
                    <td data-label="Threshold">{{ $item->low_stock_threshold }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">No low stock incidents recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Predictive Analytics for OPEX</h2>
            <div class="module-note">Forecasted next-term demand based on the most recent approved quantities.</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item</th><th>Recent Approved Quantities</th><th>Forecast Next Term</th><th>Current Stock</th><th>Action Insight</th></tr>
            </thead>
            <tbody>
                @forelse($forecastItems as $forecast)
                <tr>
                    <td data-label="Item">{{ $forecast['item_name'] }}</td>
                    <td data-label="Recent Approved Quantities">{{ $forecast['basis'] }}</td>
                    <td data-label="Forecast Next Term"><span class="status pending">{{ $forecast['forecast_next_term'] }} {{ $forecast['unit'] }}</span></td>
                    <td data-label="Current Stock">{{ $forecast['current_stock'] }} {{ $forecast['unit'] }}</td>
                    <td data-label="Action Insight">
                        @if($forecast['current_stock'] < $forecast['forecast_next_term'])
                            <span class="status low">Restock recommended</span>
                        @else
                            <span class="status approved">Stock is sufficient</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">Not enough approved requisition history yet to generate a forecast.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Asset Location Report</h2>
            <div class="module-note">CAPEX asset assignment by room or area</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item Code</th><th>Asset Name</th><th>Category</th><th>Assigned Room</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($assetLocationReport as $asset)
                <tr>
                    <td data-label="Item Code">{{ $asset->item_code }}</td>
                    <td data-label="Asset Name">{{ $asset->name }}</td>
                    <td data-label="Category">{{ $asset->category->name ?? 'Uncategorized' }}</td>
                    <td data-label="Assigned Room">{{ $asset->room_assigned ?: 'Not assigned' }}</td>
                    <td data-label="Status">
                        @if($asset->room_assigned)
                            <span class="status approved">Trackable</span>
                        @else
                            <span class="status pending">Needs room assignment</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">No CAPEX assets available for location reporting.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Approval Tracking Report</h2>
            <div class="module-note">Recent requisitions with requestor, department, and approval status</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Requisition No.</th><th>Requestor</th><th>Department</th><th>Requested At</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($approvalTracking as $record)
                <tr>
                    <td data-label="Requisition No.">{{ $record->requisition_no }}</td>
                    <td data-label="Requestor">{{ $record->user->name ?? 'Unknown' }}</td>
                    <td data-label="Department">{{ $record->department->name ?? 'Unassigned' }}</td>
                    <td data-label="Requested At">{{ optional($record->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}</td>
                    <td data-label="Status"><span class="status pending">{{ $record->statusLabel() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">No requisition records available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="report-grid">
    <div class="report-box p-3">
        <h5 class="mb-2">Issuance Status Snapshot</h5>
        <div class="chart-wrap"><canvas id="issuanceStatusChart"></canvas></div>
    </div>
    <div class="report-box p-3 d-flex flex-column justify-content-between">
        <div>
            <h5 class="mb-2">Acquisition Cost Summary</h5>
            <div class="tiny mb-3">Total recorded procurement value across all acquisitions.</div>
        </div>
        <div class="stat-value" style="font-size:36px">₱{{ number_format($totals['acquisition_cost'], 2) }}</div>
        <div class="tiny-2">Based on quantity × unit cost entries.</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const baseOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } };
new Chart(document.getElementById('inventorySummaryChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($inventoryByType->pluck('item_type')) !!},
        datasets: [{ label: 'Items', data: {!! json_encode($inventoryByType->pluck('total_items')) !!}, backgroundColor: ['#8b80ff','#f0a2a0'], borderRadius: 8 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('requestStatusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($requestStatus->pluck('status')) !!},
        datasets: [{ data: {!! json_encode($requestStatus->pluck('total')) !!}, backgroundColor: ['#f3c969','#27c96f','#57d2eb','#ff4d4f','#8b80ff'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('departmentRequestChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($departmentRequests->pluck('name')) !!},
        datasets: [{ label: 'Requests', data: {!! json_encode($departmentRequests->pluck('total_requests')) !!}, backgroundColor: '#74d0e8', borderRadius: 8 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('monthlyAcquisitionChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyAcquisitions->pluck('month_num')->map(fn($m) => date('M', mktime(0,0,0,(int)$m,1)))) !!},
        datasets: [{ label: 'Cost', data: {!! json_encode($monthlyAcquisitions->pluck('total_amount')) !!}, borderColor: '#8b80ff', backgroundColor: 'rgba(139,128,255,.18)', fill: true, tension: .35 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('issuanceStatusChart'), {
    type: 'polarArea',
    data: {
        labels: {!! json_encode($issuanceStatus->pluck('status')) !!},
        datasets: [{ data: {!! json_encode($issuanceStatus->pluck('total')) !!}, backgroundColor: ['#57d2eb','#27c96f','#f3c969','#ff4d4f'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
