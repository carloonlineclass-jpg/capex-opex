@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon icon-cyan"><i class="bi bi-box-seam"></i></div>
        <div class="stat-mini mini-green">+{{ $capexCount }}</div>
        <div class="stat-label">Total Assets (CAPEX)</div>
        <div class="stat-value">{{ $capexCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="bi bi-check2-circle"></i></div>
        <div class="stat-mini mini-green">+{{ $opexCount }}</div>
        <div class="stat-label">Consumables (OPEX)</div>
        <div class="stat-value">{{ $opexCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="bi bi-clock-history"></i></div>
        <div class="stat-mini mini-green">+{{ $pending }}</div>
        <div class="stat-label">Pending Requisitions</div>
        <div class="stat-value">{{ $pending }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="stat-mini mini-red">Action Needed</div>
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-value">{{ $lowStock }}</div>
    </div>
</div>

<div class="surface p-3 mb-3"><div class="module-head mb-2"><div><h2 class="module-title">Planning Snapshot</h2><div class="module-note">Operational summary for CAPEX monitoring and OPEX forecasting.</div></div></div><div class="row g-3"><div class="col-md-4"><div class="report-stat"><div class="tiny-2">Approved / Finalized Requisitions</div><div class="stat-value" style="font-size:28px">{{ $approvedRequisitions }}</div></div></div><div class="col-md-4"><div class="report-stat"><div class="tiny-2">Forecast-Ready OPEX Items</div><div class="stat-value" style="font-size:28px">{{ $forecastReadyItems }}</div></div></div><div class="col-md-4"><div class="report-stat"><div class="tiny-2">Issued Asset Records</div><div class="stat-value" style="font-size:28px">{{ $issuedAssets }}</div></div></div></div></div>

<div class="panel-grid-2">
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-layers text-primary"></i> Inventory Classification (CAPEX vs OPEX)</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="inventoryTypeChart"></canvas></div></div>
    </div>
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-diagram-3 text-primary"></i> Resource Allocation by Department</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="allocationChart"></canvas></div></div>
    </div>
</div>

<div class="panel-grid-2">
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-activity text-primary"></i> Requisition Trends</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="requisitionTrendChart"></canvas></div></div>
    </div>
    <div class="chart-card">
        <div class="chart-head"><i class="bi bi-pie-chart text-primary"></i> Asset Category Distribution</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="categoryDistributionChart"></canvas></div></div>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Low Stock Items</h2>
            <div class="module-note">Consumables that need replenishment soon</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Code</th><th>Name</th><th>Category</th><th>Stock</th><th>Threshold</th></tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                    <tr>
                        <td data-label="Code">{{ $item->item_code }}</td>
                        <td data-label="Name">{{ $item->name }}</td>
                        <td data-label="Category">{{ $item->category->name ?? 'Office Supplies' }}</td>
                        <td data-label="Stock"><span class="status low">{{ $item->quantity }}</span></td>
                        <td data-label="Threshold">{{ $item->low_stock_threshold }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No low stock items at the moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel-grid-2">
    <div class="data-panel">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title">Recent Requisitions</h2>
                <div class="module-note">Latest requests submitted in the system</div>
            </div>
            <a href="{{ route('requisitions.index') }}" class="btn-soft small-btn">Open Module</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Reference</th><th>Requester</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentRequisitions as $req)
                    <tr>
                        <td><a href="{{ route('requisitions.show', $req) }}">{{ $req->requisition_no }}</a></td>
                        <td>{{ $req->user->name ?? 'Unknown User' }}</td>
                        <td><span class="status {{ $req->status === 'approved' ? 'approved' : ($req->status === 'rejected' ? 'low' : 'pending') }}">{{ ucfirst($req->status) }}</span></td>
                        <td>{{ optional($req->requested_at)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No recent requisitions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="data-panel">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title">Recent Acquisitions</h2>
                <div class="module-note">Latest incoming asset and supply records</div>
            </div>
            @if(auth()->user()->isAdmin())<a href="{{ route('acquisitions.index') }}" class="btn-soft small-btn">Open Module</a>@endif
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Item</th><th>Supplier</th><th>Qty</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentAcquisitions as $acquisition)
                    <tr>
                        <td>{{ $acquisition->item->name ?? 'Unknown Item' }}</td>
                        <td>{{ $acquisition->supplier->name ?? 'Unknown Supplier' }}</td>
                        <td>{{ $acquisition->quantity }}</td>
                        <td>{{ $acquisition->acquisition_date ? \Illuminate\Support\Carbon::parse($acquisition->acquisition_date)->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No recent acquisitions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { boxWidth: 12 } } },
    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
};
new Chart(document.getElementById('inventoryTypeChart'), {
    type: 'bar',
    data: {
        labels: ['Inventory'],
        datasets: [
            { label: 'CAPEX (Assets)', data: [{{ $capexCount }}], backgroundColor: '#8b80ff', borderRadius: 6 },
            { label: 'OPEX (Consumables)', data: [{{ $opexCount }}], backgroundColor: '#f0a2a0', borderRadius: 6 }
        ]
    },
    options: chartDefaults
});
new Chart(document.getElementById('allocationChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($allocationByDepartment->pluck('name')) !!},
        datasets: [
            { label: 'CAPEX', data: {!! json_encode($allocationByDepartment->pluck('capex')) !!}, backgroundColor: '#8b80ff', borderRadius: 6 },
            { label: 'OPEX', data: {!! json_encode($allocationByDepartment->pluck('opex')) !!}, backgroundColor: '#74d0e8', borderRadius: 6 }
        ]
    },
    options: chartDefaults
});
new Chart(document.getElementById('requisitionTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($requisitionTrend->pluck('month_num')->map(fn($m) => date('M', mktime(0,0,0,(int)$m,1)))) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($requisitionTrend->pluck('total')) !!},
            borderColor: '#8b80ff',
            backgroundColor: 'rgba(139,128,255,.18)',
            tension: .35,
            fill: true,
            pointRadius: 4
        }]
    },
    options: chartDefaults
});
new Chart(document.getElementById('categoryDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryDistribution->pluck('category_name')) !!},
        datasets: [{
            data: {!! json_encode($categoryDistribution->pluck('total')) !!},
            backgroundColor: ['#8b80ff','#74d0e8','#f0a2a0','#7bd389','#f3c969','#91a7ff']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
