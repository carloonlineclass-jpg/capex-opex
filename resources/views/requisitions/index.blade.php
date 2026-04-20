@extends('layouts.admin', ['title' => 'Requisitions'])
@section('content')
<div class="module-head"><div><h2 class="module-title">Requisitions</h2><div class="module-note">Charge slip requests routed through Asset Management, College Dean, and Executive Director.</div></div>@if(auth()->user()->isRequestor() || auth()->user()->isAdmin())<a href="{{ route('requisitions.create') }}" class="btn-primaryx"><i class="bi bi-plus-lg"></i> New Request</a>@endif</div>
<div class="page-tabs"><span class="active">Request Monitoring</span><span>{{ auth()->user()->isAdmin() ? 'Asset Management View' : (auth()->user()->isApprover() ? 'Approver Queue' : 'My Requests') }}</span></div>
<div class="surface p-3">
    <form method="GET" class="search-strip mb-3">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Search by reference, requester, or item...">
        <div class="filter-box"><i class="bi bi-funnel text-muted"></i>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending_asset_management' => 'Asset Management','pending_college_dean' => 'College Dean','pending_executive_director' => 'Executive Director','approved' => 'Approved','partially_approved' => 'Partially Approved','rejected' => 'Rejected'] as $key => $label)
                <option value="{{ $key }}" @selected(($status ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primaryx small-btn" type="submit">Apply</button>
    </form>
    @forelse($requisitions as $requisition)
    <div class="request-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap"><span style="font-weight:800">{{ $requisition->requisition_no }}</span><span class="status {{ str_contains($requisition->status,'approved') ? 'approved' : ($requisition->status === 'rejected' ? 'low' : 'pending') }}">{{ $requisition->statusLabel() }}</span><span class="tiny"><i class="bi bi-calendar-event"></i> {{ optional($requisition->requested_at)->format('Y-m-d') }}</span></div>
                <div style="font-weight:700;margin-top:4px">{{ $requisition->department->name ?? 'No Department' }} · {{ $requisition->branch ?: 'NU Clark' }}</div>
                <div class="tiny">Requested by: {{ $requisition->user->name ?? 'Unknown User' }}</div>
                <div class="tiny-2 mt-1">Purpose: {{ $requisition->purpose ?: 'No purpose stated.' }}</div>
                <div class="tiny-2 mt-1">Items: {{ $requisition->items->map(fn($line) => ($line->item->name ?? 'Item').' x'.$line->quantity_requested)->join(', ') }}</div>
                @if($requisition->status === 'rejected' && $requisition->rejection_reason)
                <div class="tiny mt-2 text-danger"><strong>Reason:</strong> {{ $requisition->rejection_reason }}</div>
                @endif
            </div>
            <div class="request-actions">
                <a class="btn-approve" href="{{ route('requisitions.show', $requisition) }}"><i class="bi bi-eye"></i> View</a>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">No requisitions found.</div>
    @endforelse
    {{ $requisitions->links() }}
</div>
@endsection
