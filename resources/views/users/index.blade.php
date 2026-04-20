@extends('layouts.admin', ['title' => 'User Management'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">User Access Management</h2>
        <div class="module-note">Manage user roles, department assignments, and account approval.</div>
    </div>
</div>

<div class="surface p-3 mb-3">
    <form method="GET" class="search-strip mb-0">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or role...">
        <button class="btn-primaryx" type="submit"><i class="bi bi-funnel"></i> Filter</button>
    </form>
</div>

<div class="surface p-3">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Approver Type</th>
                    <th>Approval</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td data-label="User">
                        <div style="font-weight:700">{{ $user->name }}</div>
                        <div class="tiny">{{ $user->email }}</div>
                    </td>
                    <td data-label="Department">{{ $user->department->name ?? 'Not assigned' }}</td>
                    <td data-label="Role"><span class="status {{ $user->role === 'admin' ? 'approved' : ($user->role === 'approver' ? 'available' : 'pending') }}">{{ ucfirst($user->role) }}</span></td>
                    <td data-label="Approver Type">{{ $user->approver_type ? ucfirst($user->approver_type) : '-' }}</td>
                    <td data-label="Approval">
                        <span class="status {{ $user->is_approved ? 'approved' : 'pending' }}">{{ $user->is_approved ? 'Approved' : 'Pending Review' }}</span>
                        <div class="tiny-2 mt-1">{{ $user->email_verified_at ? 'Email verified' : 'Email not verified' }}</div>
                    </td>
                    <td data-label="Actions">
                        <button class="btn-soft small-btn" type="button" data-bs-toggle="collapse" data-bs-target="#edit-user-{{ $user->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user account?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-reject small-btn" type="submit"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                <tr class="collapse-row">
                    <td colspan="6" class="p-0 border-0">
                        <div class="collapse" id="edit-user-{{ $user->id }}">
                            <div class="p-3" style="background:#f7f8fb;border-top:1px solid #e2e5ee">
                                <form method="POST" action="{{ route('users.update', $user) }}" class="row g-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                        <input class="form-control" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" type="email" name="email" value="{{ $user->email }}" readonly aria-readonly="true">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Role</label>
                                        <select class="form-select role-select" name="role" data-user-id="{{ $user->id }}">
                                            @foreach($roles as $role)
                                            <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 approver-type-wrapper" id="approver-type-wrapper-{{ $user->id }}">
                                        <label class="form-label">Approver Type</label>
                                        <select class="form-select approver-type-select" name="approver_type">
                                            <option value="">None</option>
                                            @foreach($approverTypes as $type)
                                            <option value="{{ $type }}" @selected($user->approver_type === $type)>{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department_id">
                                            <option value="">No department</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @selected((int) $user->department_id === (int) $department->id)>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Approval</label>
                                        <select class="form-select" name="is_approved">
                                            <option value="0" @selected(!$user->is_approved)>Pending Review</option>
                                            <option value="1" @selected($user->is_approved)>Approved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button class="btn-primaryx w-100 justify-content-center" type="submit"><i class="bi bi-save"></i> Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-state">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.role-select').forEach(function (roleSelect) {
        const userId = roleSelect.dataset.userId;
        const wrapper = document.getElementById('approver-type-wrapper-' + userId);
        if (!wrapper) return;

        const approverTypeSelect = wrapper.querySelector('.approver-type-select');

        function toggleApproverType() {
            const isApprover = roleSelect.value === 'approver';
            wrapper.style.display = isApprover ? '' : 'none';
            if (!isApprover && approverTypeSelect) {
                approverTypeSelect.value = '';
            }
        }

        toggleApproverType();
        roleSelect.addEventListener('change', toggleApproverType);
    });
});
</script>
@endpush
@endsection

