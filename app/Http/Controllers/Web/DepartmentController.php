<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->orderBy('name')->paginate(10);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        Department::create($request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:50','unique:departments,code'],
            'capex_limit' => ['required','integer','min:0'],
            'opex_limit' => ['required','integer','min:0'],
        ]));

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $department->update($request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['required','string','max:50','unique:departments,code,'.$department->id],
            'capex_limit' => ['required','integer','min:0'],
            'opex_limit' => ['required','integer','min:0'],
        ]));

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
