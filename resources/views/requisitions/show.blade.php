@extends('layouts.admin', ['title' => 'Requisition Details'])
@section('content')
<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:18px">{{ $requisition->requisition_no }}</h2>
                <div class="module-note">Charge slip requisition details</div>
            </div>
            <span class="status {{ str_contains($requisition->status,'approved') ? 'approved' : ($requisition->status === 'rejected' ? 'low' : 'pending') }}">{{ $requisition->statusLabel() }}</span>
        </div>

        <div class="row g-2 mb-3 tiny">
            <div class="col-md-6"><strong>Branch:</strong> {{ $requisition->branch ?: 'NU Clark' }}</div>
            <div class="col-md-6"><strong>Department:</strong> {{ $requisition->department->name ?? 'N/A' }}</div>
            <div class="col-md-6"><strong>Charge To:</strong> {{ $requisition->charge_to_budget_item ?: 'N/A' }}</div>
            <div class="col-md-6"><strong>CSF No.:</strong> {{ $requisition->csf_no ?: 'N/A' }}</div>
            <div class="col-md-6"><strong>Requested By:</strong> {{ $requisition->requested_by_name ?: ($requisition->user->name ?? 'N/A') }}</div>
            <div class="col-md-6"><strong>Date Requested:</strong> {{ optional($requisition->requested_at)->format('Y-m-d H:i') ?: 'N/A' }}</div>
            <div class="col-12"><strong>Purpose:</strong> {{ $requisition->purpose ?: 'N/A' }}</div>
        </div>

        <table class="data-table">
            <thead><tr><th>Item</th><th>Requested</th><th>Approved</th><th>Available Stock</th><th>Remarks</th></tr></thead>
            <tbody>
                @foreach($requisition->items as $item)
                <tr>
                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity_requested }}</td>
                    <td>{{ $item->quantity_approved ?? '-' }}</td>
                    <td>{{ $item->item->quantity ?? '-' }}</td>
                    <td>{{ $item->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 tiny">
            <div><strong>Asset Management Reviewed By:</strong> {{ $requisition->assetReviewer->name ?? 'Pending' }}</div>
            <div><strong>College Dean Approved By:</strong> {{ $requisition->deanApprover->name ?? 'Pending' }}</div>
            <div><strong>Executive Director Approved By:</strong> {{ $requisition->executiveApprover->name ?? 'Pending' }}</div>
        </div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title" style="font-size:16px">Actions</h3>

        @if($requisition->status === 'rejected')
            <div class="alert alert-danger">Rejected: {{ $requisition->rejection_reason }}</div>
        @endif

        @if(auth()->user()->isAdmin() && $requisition->isAwaitingAssetManagement())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">
                @csrf
                <p class="tiny">Asset Management may cut the requested quantity based on available stock.</p>
                @foreach($requisition->items as $line)
                    <div class="border rounded-3 p-2 mb-2">
                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $line->id }}">
                        <div class="tiny mb-1"><strong>{{ $line->item->name }}</strong> · Requested {{ $line->quantity_requested }} · Available {{ $line->item->quantity }}</div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Approved Qty</label>
                                <input type="number" name="items[{{ $loop->index }}][quantity_approved]" class="form-control" min="0" max="{{ $line->quantity_requested }}" value="{{ old('items.'.$loop->index.'.quantity_approved', $line->quantity_approved ?? min($line->quantity_requested, $line->item->quantity)) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="items[{{ $loop->index }}][remarks]" class="form-control" value="{{ old('items.'.$loop->index.'.remarks', $line->remarks) }}" placeholder="Example: cut to available stock">
                            </div>
                        </div>
                    </div>
                @endforeach
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Forward to College Dean</button>
            </form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @elseif(auth()->user()->isDeanApprover() && $requisition->isAwaitingCollegeDean())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">@csrf<button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Approve and Forward to Executive Director</button></form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @elseif(auth()->user()->isExecutiveApprover() && $requisition->isAwaitingExecutiveDirector())
            <form method="POST" action="{{ route('requisitions.approve',$requisition) }}" class="mb-3">@csrf<button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Final Approve Requisition</button></form>
            <form method="POST" action="{{ route('requisitions.reject',$requisition) }}">@csrf
                <label class="form-label">Rejection Reason</label>
                <textarea class="form-control mb-3" name="reason" required></textarea>
                <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Reject Request</button>
            </form>
        @else
            <div class="empty-state">No action available for your account at the current stage.</div>
        @endif
    </div>
</div>
@endsection
