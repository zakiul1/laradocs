<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    // --------------- Validation rules as per spec ---------------
    protected function rules(?int $id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('employees', 'phone')->ignore($id)],
            'email' => ['nullable', 'email', Rule::unique('employees', 'email')->ignore($id)],
            'designation' => ['nullable', 'string', 'max:100'],
            'join_date' => ['required', 'date'],
            'leave_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'documents.*' => ['nullable', 'file', 'max:5120'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive', 'Resigned'])],
            'address' => ['nullable', 'string'],
            'alternative_contact_number' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    // --------------- Index with search/filter/sort/paginate ---------------
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $gender = $request->string('gender')->toString();
        $status = $request->string('status')->toString();
        $designation = $request->string('designation')->toString();

        $sort = $request->string('sort', 'created_at')->toString();
        $dir = $request->string('dir', 'desc')->toString();

        $employees = Employee::query()
            ->when(
                $q,
                fn(Builder $query) =>
                $query->where(function ($x) use ($q) {
                    $x->where('name', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('designation', 'like', "%$q%");
                })
            )
            ->when($gender, fn(Builder $query) => $query->where('gender', $gender))
            ->when($status, fn(Builder $query) => $query->where('status', $status))
            ->when($designation, fn(Builder $query) => $query->where('designation', $designation))
            ->orderBy(
                in_array($sort, ['name', 'phone', 'email', 'designation', 'status', 'join_date', 'created_at']) ? $sort : 'created_at',
                in_array($dir, ['asc', 'desc']) ? $dir : 'desc'
            )
            ->paginate(12)
            ->withQueryString();

        // Unique designations for filter
        $designations = Employee::query()
            ->select('designation')
            ->whereNotNull('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation');

        return view('admin.employees.index', compact('employees', 'q', 'gender', 'status', 'designation', 'designations', 'sort', 'dir'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    // --------------- Store (AJAX-friendly) ---------------
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $data = $validated;
        $data['created_by'] = Auth::id();

        // Handle photo
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        // Handle documents (array of files)
        $docs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $docs[] = $file->store('employees/docs', 'public');
            }
        }
        if ($docs) {
            $data['documents'] = $docs;
        }

        $employee = Employee::create($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Employee created successfully', 'id' => $employee->id]);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    // --------------- Update (replace files safely) ---------------
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->rules($employee->id));
        $data = $validated;

        // Replace photo if new file uploaded or if user chose to remove it
        if ($request->boolean('remove_photo') && $employee->photo) {
            Storage::disk('public')->delete($employee->photo);
            $data['photo'] = null;
        }
        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        // Handle documents: append new, optionally remove selected
        $existingDocs = $employee->documents ?? [];
        $removeDocs = $request->input('remove_documents', []); // array of stored paths to remove
        if ($removeDocs) {
            foreach ($removeDocs as $path) {
                if (in_array($path, $existingDocs, true)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $existingDocs = array_values(array_diff($existingDocs, $removeDocs));
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $existingDocs[] = $file->store('employees/docs', 'public');
            }
        }
        $data['documents'] = $existingDocs;

        $employee->update($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Employee updated successfully']);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }

    // --------------- Destroy (soft by default, hard if ?force=1) ---------------
    public function destroy(Request $request, Employee $employee)
    {
        $force = $request->boolean('force');

        if ($force) {
            // delete files first
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            foreach (($employee->documents ?? []) as $path) {
                Storage::disk('public')->delete($path);
            }
            $employee->forceDelete();
        } else {
            $employee->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Employee deleted']);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted');
    }
}