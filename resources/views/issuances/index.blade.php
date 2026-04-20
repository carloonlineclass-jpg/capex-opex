@extends('layouts.admin', ['title' => 'Issuance & Returns'])
@section('content')
<div class="module-head"><div><h2 class="module-title">Issuance & Returns</h2><div class="module-note">Process approved requests and mark issued items as returned</div></div><a href="{{ route('issuances.create') }}" class="btn-primaryx"><i class="bi bi-plus-lg"></i> New Issuance</a></div>
<div class="page-tabs"><span class="active"><i class="bi bi-arrow-up-right-circle"></i> Issued ({{ $issuances->where('status','issued')->count() }})</span><span><i class="bi bi-arrow-counterclockwise"></i> Returned ({{ $issuances->where('status','returned')->count() }})</span></div>
<div class="surface p-3">
    <div class="search-strip"><i class="bi bi-search text-muted"></i><input class="search-input" placeholder="Search approved requests..."></div>
    @forelse($issuances as $issuance)
    <div class="issue-card">
        <div class="d-flex justify-content-between gap-4 align-items-start flex-wrap">
            <div>
                <div style="font-weight:800">{{ $issuance->requisition->requisition_no ?? 'N/A' }} <span class="status approved">{{ ucfirst($issuance->status) }}</span></div>
                <div class="tiny">Requester: {{ $issuance->receiver->name ?? 'N/A' }} ({{ $issuance->requisition->department->name ?? 'Department' }})</div>
                <div class="tiny mt-2"><strong>ITEM TO ISSUE:</strong></div>
                @forelse($issuance->requisition->items as $reqItem)
                <div class="tiny">- {{ $reqItem->quantity_requested }}x {{ $reqItem->item->name ?? 'Item' }} <span class="pill-opex">{{ $reqItem->item->item_type ?? 'OPEX' }}</span></div>
                @empty
                <div class="tiny">No linked item found.</div>
                @endforelse
            </div>
            <div class="d-grid gap-2" style="min-width:170px">
                @if($issuance->status === 'issued')
                    <form method="POST" action="{{ route('issuances.return',$issuance) }}">
                        @csrf
                        <button type="submit" class="btn-primaryx small-btn w-100"><i class="bi bi-arrow-counterclockwise"></i> To Return</button>
                    </form>
                @else
                    <button type="button" class="btn btn-light btn-sm w-100" style="border-radius:8px;border:1px solid #c9ced6" disabled>Already Returned</button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">No issuance records found.</div>
    @endforelse
    {{ $issuances->links() }}
</div>
@endsection
