<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryCategory;
use App\Models\FactorySubcategory;
use Illuminate\Http\Request;

class FactorySubcategoryController extends Controller
{
    public function index()
    {
        $categories = FactoryCategory::orderBy('position')->orderBy('name')->get();
        $subcategories = FactorySubcategory::with('category')
            ->orderBy('factory_category_id')->orderBy('position')->orderBy('name')
            ->paginate(50);

        return view('admin.factory-subcategories.index', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'factory_category_id' => ['required', 'exists:factory_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);
        FactorySubcategory::create([
            'factory_category_id' => $data['factory_category_id'],
            'name' => $data['name'],
            'position' => $data['position'] ?? 0,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);
        return back()->with('success', 'Subcategory created.');
    }

    public function update(Request $request, FactorySubcategory $factorySubcategory)
    {
        $data = $request->validate([
            'factory_category_id' => ['required', 'exists:factory_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);
        $factorySubcategory->update([
            'factory_category_id' => $data['factory_category_id'],
            'name' => $data['name'],
            'position' => $data['position'] ?? 0,
            'updated_by' => $request->user()?->id,
        ]);
        return back()->with('success', 'Subcategory updated.');
    }

    public function destroy(FactorySubcategory $factorySubcategory)
    {
        $factorySubcategory->delete();
        return back()->with('success', 'Subcategory deleted.');
    }
}