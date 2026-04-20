@extends('layouts.admin', ['title' => 'Acquisition Records'])
@section('content')
<div class="module-head"><div><h2 class="module-title">Acquisition Records</h2><div class="module-note">Track procurement, deliveries, and replenishment</div></div><a href="{{ route('acquisitions.create') }}" class="btn-primaryx"><i class="bi bi-plus-lg"></i> Record Acquisition</a></div>
<div class="surface p-3">
    <div class="table-responsive">
    <table class="data-table">
        <thead><tr><th>Date</th><th>Item</th><th>Supplier</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @forelse($acquisitions as $acquisition)
            <tr>
                <td>{{ $acquisition->acquisition_date }}</td>
                <td><div style="font-weight:700">{{ $acquisition->item->name ?? 'N/A' }}</div><div class="tiny">{{ $acquisition->remarks ?: 'Acquisition entry' }}</div></td>
                <td>{{ $acquisition->supplier->name ?? 'N/A' }}</td>
                <td>{{ $acquisition->quantity }}</td>
                <td>₱{{ number_format($acquisition->unit_cost,2) }}</td>
                <td style="font-weight:700">₱{{ number_format($acquisition->total_cost,2) }}</td>
                <td class="text-end"><a class="btn-soft small-btn" href="{{ route('acquisitions.edit',$acquisition) }}"><i class="bi bi-pencil"></i></a><form class="d-inline" method="POST" action="{{ route('acquisitions.destroy',$acquisition) }}">@csrf @method('DELETE')<button class="btn-soft small-btn"><i class="bi bi-three-dots-vertical"></i></button></form></td>
            </tr>
            @empty
            <tr><td colspan="7" class="empty-state">No acquisitions recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $acquisitions->links() }}
</div>
@endsection