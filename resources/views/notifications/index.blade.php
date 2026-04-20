@extends('layouts.admin', ['title' => 'Notifications'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">Notifications</h2>
        <div class="module-note">Email and in-system updates for requisition routing and approvals.</div>
    </div>
    @if(auth()->user()->unreadNotifications->count())
    <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn-primaryx"><i class="bi bi-check2-all"></i> Mark All as Read</button></form>
    @endif
</div>

<div class="surface p-3">
    @forelse($notifications as $notification)
    @php($data = $notification->data)
    <div class="request-card" style="background:{{ $notification->read_at ? '#d8d8d8' : '#eef4ff' }}">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <strong>{{ $data['subject'] ?? 'System Notification' }}</strong>
                    @if(!$notification->read_at)
                    <span class="status pending">Unread</span>
                    @endif
                </div>
                <div class="tiny mt-1">{{ $data['message'] ?? 'No message available.' }}</div>
                <div class="tiny-2 mt-2">{{ $data['requisition_no'] ?? 'N/A' }} · {{ $data['status_label'] ?? 'N/A' }} · {{ $notification->created_at?->format('Y-m-d h:i A') }}</div>
            </div>
            <div class="request-actions">
                @if(!$notification->read_at)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf<button class="btn-soft"><i class="bi bi-check-lg"></i> Mark Read</button></form>
                @endif
                <a class="btn-approve" href="{{ $data['url'] ?? route('requisitions.index') }}"><i class="bi bi-eye"></i> Open</a>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">No notifications yet.</div>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection
