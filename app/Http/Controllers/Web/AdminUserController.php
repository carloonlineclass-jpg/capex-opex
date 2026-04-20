<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $users = User::query()
            ->with('department')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('approver_type', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN is_approved = 0 THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 WHEN role = 'approver' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
            'roles' => ['admin', 'approver', 'requestor'],
            'approverTypes' => ['dean', 'executive'],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'approver', 'requestor'])],
            'approver_type' => ['nullable', Rule::in(['dean', 'executive'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        }

        if ($user->role === 'admin' && $data['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'At least one admin account must remain in the system.']);
            }
        }

        if ($data['role'] !== 'approver') {
            $data['approver_type'] = null;
        }

        $data['is_approved'] = $request->boolean('is_approved');
        $data['approved_at'] = $data['is_approved'] ? ($user->approved_at ?? now()) : null;

        $user->update($data);

        return back()->with('success', 'User profile and role updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account while logged in.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'At least one admin account must remain in the system.']);
        }

        $user->delete();

        return back()->with('success', 'User account deleted successfully.');
    }
}
