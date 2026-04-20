@extends('layouts.admin', ['title' => 'Settings'])
@section('content')
<div class="module-head"><div><h2 class="module-title">Settings</h2><div class="module-note">System preferences and administrative controls</div></div></div>
<div class="settings-list">
    @if(auth()->user()->role === 'admin')
    <div class="settings-item"><h5>User Role Management</h5><p class="tiny mb-2">Assign admin, approver (dean/executive), and requestor access from the user management panel.</p><a href="{{ route('users.index') }}" class="btn-primaryx"><i class="bi bi-people"></i> Open User Management</a></div>
    @endif
    <div class="settings-item"><h5>General Preferences</h5><p class="tiny mb-0">Configure application labels, request numbering, and mail driver values in the .env file before deployment.</p></div>
    <div class="settings-item"><h5>User Access</h5><p class="tiny mb-0">Control role permissions for administrators, approvers, and requestors.</p></div>
    <div class="settings-item"><h5>Notification Rules</h5><p class="tiny mb-0">The package now sends database notifications and email alerts for approval routing. Default mailer is safe for local testing.</p></div>
</div>
@endsection