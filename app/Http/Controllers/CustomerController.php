<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $country = $request->get('country');
        $designation = $request->get('designation');
        $sort = in_array($request->get('sort'), ['name', 'email', 'phone', 'country', 'company_name', 'designation', 'created_at']) ? $request->get('sort') : 'created_at';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $customers = Customer::query()
            ->when($q, fn($qr) => $qr->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('designation', 'like', "%{$q}%");
            }))
            ->when($country, fn($qr) => $qr->where('country', $country))
            ->when($designation, fn($qr) => $qr->where('designation', $designation))
            ->orderBy($sort, $dir)
            ->paginate(12)
            ->withQueryString();

        // For filter dropdowns (optional – build from existing data)
        $countries = Customer::query()->whereNotNull('country')->distinct()->pluck('country')->sort()->values();
        $designations = Customer::query()->whereNotNull('designation')->distinct()->pluck('designation')->sort()->values();

        return view('customers.index', compact('customers', 'countries', 'designations'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // set created_by if available
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        // handle photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('customers/photos', 'public');
        }

        // handle documents (array)
        $docsPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file) {
                    $docsPaths[] = $file->store('customers/docs', 'public');
                }
            }
        }
        if (!empty($docsPaths)) {
            $validated['documents'] = $docsPaths;
        }

        $customer = Customer::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Customer created successfully.',
                'id' => $customer->id,
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $this->validateData($request, $customer->id);

        // Photo replacement / removal
        if ($request->boolean('remove_photo')) {
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }
            $validated['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('customers/photos', 'public');
        }

        // Documents removal (existing)
        $existingDocs = $customer->documents ?? [];
        $toRemove = (array) $request->input('remove_documents', []);
        if (!empty($toRemove)) {
            foreach ($toRemove as $path) {
                Storage::disk('public')->delete($path);
            }
            $existingDocs = array_values(array_diff($existingDocs, $toRemove));
        }

        // Documents add (new)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file) {
                    $existingDocs[] = $file->store('customers/docs', 'public');
                }
            }
        }
        $validated['documents'] = $existingDocs;

        $customer->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Customer updated successfully.',
                'id' => $customer->id,
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        // delete files (photo + documents) to keep storage clean
        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }
        if ($customer->documents) {
            foreach ($customer->documents as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $customer->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Customer deleted successfully.']);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'designation' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'documents.*' => ['nullable', 'file', 'max:5120'],
        ]);
    }
}