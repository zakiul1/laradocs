<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(12);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // BD mobile formats: +8801XXXXXXXXX or 01XXXXXXXXX
            'phone' => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],

            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'address' => ['nullable', 'string', 'max:500'],
            'designation' => 'nullable|string|max:255',

            'join_date' => ['nullable', 'date'],
            'leave_date' => ['nullable', 'date', 'after_or_equal:join_date'],

            // File uploads — larger and clearer limits
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'], // 10 MB
            'documents' => ['nullable', 'array', 'max:20'],                         // up to 20 files
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'], // 20 MB each
        ], [
            'phone.regex' => 'Use Bangladeshi format, e.g. 01XXXXXXXXX or +8801XXXXXXXXX.',
            'photo.image' => 'Photo must be a valid image (JPG or PNG).',
            'photo.max' => 'Photo size cannot exceed 10 MB.',
            'documents.*.mimes' => 'Each document must be a PDF, Word, Excel, or image file.',
            'documents.*.max' => 'Each document must not exceed 20 MB.',
            'documents.max' => 'You can upload at most 20 documents.',
            'leave_date.after_or_equal' => 'Leave date cannot be before join date.',
        ]);


        // store employee
        $employee = new Employee($data);

        // photo upload
        if ($request->hasFile('photo')) {
            $employee->photo_path = $request->file('photo')->store('employees/photos', 'public');
        }
        $employee->save();

        // documents upload (multiple)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('employees/documents', 'public');
                $employee->documents()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.employees.index')->with('status', 'Employee created.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('documents');
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id), // <-- important
            ],
            'designation' => 'nullable|string|max:255',
            'address' => ['nullable', 'string', 'max:500'],
            'join_date' => ['nullable', 'date'],
            'leave_date' => ['nullable', 'date', 'after_or_equal:join_date'],

            // keep your file limits as you set earlier
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'documents' => ['nullable', 'array', 'max:20'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'],
        ], [
            'phone.regex' => 'Use Bangladeshi format, e.g. 01XXXXXXXXX or +8801XXXXXXXXX.',
        ]);

        // replace / remove photo
        if ($request->boolean('remove_photo') && $employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
            $employee->photo_path = null;
        }
        if ($request->hasFile('photo')) {
            if ($employee->photo_path)
                Storage::disk('public')->delete($employee->photo_path);
            $employee->photo_path = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->fill($data)->save();

        // remove selected documents
        if ($request->filled('remove_docs')) {
            $docs = EmployeeDocument::whereIn('id', $request->input('remove_docs'))->get();
            foreach ($docs as $doc) {
                Storage::disk('public')->delete($doc->path);
                $doc->delete();
            }
        }
        // add new documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('employees/documents', 'public');
                $employee->documents()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.employees.edit', $employee)->with('status', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        // delete files
        if ($employee->photo_path)
            Storage::disk('public')->delete($employee->photo_path);
        foreach ($employee->documents as $doc) {
            Storage::disk('public')->delete($doc->path);
        }
        $employee->documents()->delete();
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('status', 'Employee deleted.');
    }
}