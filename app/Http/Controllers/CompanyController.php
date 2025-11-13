<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $category = $request->get('category'); // id
        $sort = in_array($request->get('sort'), ['company_name', 'email', 'phone', 'created_at'])
            ? $request->get('sort')
            : 'created_at';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $companies = Company::query()
            ->with('category')
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('company_name', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('contact_person', 'like', "%{$q}%")
                        ->orWhere('website', 'like', "%{$q}%")
                        ->orWhere('note', 'like', "%{$q}%");
                });
            })
            ->when($category, fn($qr) => $qr->where('company_category_id', $category))
            ->orderBy($sort, $dir)
            ->paginate(12)
            ->withQueryString();

        $categories = CompanyCategory::orderBy('name')->get();

        return view('companies.index', compact('companies', 'categories'));
    }

    public function create()
    {
        $categories = CompanyCategory::orderBy('name')->get();

        return view('companies.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        $company = Company::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Company created successfully.',
                'id' => $company->id,
            ]);
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company created.');
    }

    public function show(Company $company)
    {
        $company->load('category', 'creator');

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $categories = CompanyCategory::orderBy('name')->get();

        return view('companies.edit', compact('company', 'categories'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $this->validated($request);

        $company->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Company updated successfully.',
                'id' => $company->id,
            ]);
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Request $request, Company $company)
    {
        $company->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Company deleted successfully.']);
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company deleted.');
    }

    /**
     * Quick create category (real-time, from create/edit form)
     */
    public function quickCreateCategory(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('company_categories', 'name')],
        ]);

        $category = new CompanyCategory();
        $category->name = $data['name'];
        $category->slug = Str::slug($data['name']);
        if (auth()->check()) {
            $category->created_by = auth()->id();
        }
        $category->save();

        return response()->json([
            'message' => 'Category created.',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ], 201);
    }

    /**
     * Optional: small JSON endpoint to search categories (for future if you want).
     */
    public function categoriesJson(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $cats = CompanyCategory::query()
            ->when($q, fn($qr) => $qr->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json([
            'data' => $cats,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'company_category_id' => ['nullable', 'exists:company_categories,id'],
        ]);
    }
}