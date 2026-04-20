@extends('layouts.admin', ['title' => 'Departments'])
@section('content')
<div class="module-head"><div><h2 class="module-title">Department Allocation</h2><div class="module-note">Manage department profiles and budget limits</div></div><a href="{{ route('departments.create') }}" class="btn-primaryx"><i class="bi bi-plus-lg"></i> Add Department</a></div>
<div class="surface p-3">
    <div class="search-strip"><i class="bi bi-search text-muted"></i><input class="search-input" placeholder="Search departments..."><div class="filter-box"><i class="bi bi-funnel text-muted"></i><select><option>All</option><option>High CAPEX</option></select></div></div>
    <div class="table-responsive">
    <table class="data-table">
        <thead><tr><th>Department</th><th>Code</th><th>CAPEX Limit</th><th>OPEX Limit</th><th>Users</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @forelse($departments as $department)
            <tr>
                <td><div style="font-weight:700">{{ $department->name }}</div><div class="tiny">Budget controls and request routing</div></td>
                <td>{{ $department->code }}</td>
                <td>{{ $department->capex_limit }}</td>
                <td>{{ $department->opex_limit }}</td>
                <td>{{ $department->users()->count() }}</td>
                <td class="text-end"><a class="btn-soft small-btn" href="{{ route('departments.edit',$department) }}"><i class="bi bi-pencil"></i></a><form class="d-inline" method="POST" action="{{ route('departments.destroy',$department) }}">@csrf @method('DELETE')<button class="btn-soft small-btn"><i class="bi bi-three-dots-vertical"></i></button></form></td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state">No departments found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $departments->links() }}
</div>
@endsection