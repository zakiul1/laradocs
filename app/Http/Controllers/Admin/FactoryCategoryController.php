<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FactoryCategoryController extends Controller
{
    public function index()
    {
        // All categories (parents and children) for the main table
        $categories = FactoryCategory::with('parent')
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(30);

        // Subcategories tab: only records that have a parent
        $subcategories = FactoryCategory::with('parent')
            ->whereNotNull('factory_category_id')
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(30);

        // Full flat list for parent pickers (WP-style form)
        $allCategories = FactoryCategory::orderBy('name')->get();

        return view('admin.factory-categories.index', compact('categories', 'subcategories', 'allCategories'));
    }

    /**
     * Create a category.
     * - If "factory_category_id" is null => parent category
     * - If "factory_category_id" has a value => child (subcategory) of that parent
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'factory_category_id' => ['nullable', 'integer', Rule::exists('factory_categories', 'id')],
            'name' => ['required', 'string', 'max:255', Rule::unique('factory_categories', 'name')],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        FactoryCategory::create([
            'factory_category_id' => $data['factory_category_id'] ?? null,
            'name' => $data['name'],
            'position' => $data['position'] ?? 0,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('success', isset($data['factory_category_id']) ? 'Subcategory created.' : 'Category created.');
    }

    /**
     * Back-compat endpoint for explicitly creating a child.
     * (Old routes may still POST here; we delegate to store().)
     */
    public function storeChild(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Update a category (also supports re-parenting).
     */
    public function update(Request $request, FactoryCategory $factoryCategory)
    {
        $data = $request->validate([
            'factory_category_id' => [
                'nullable',
                'integer',
                Rule::exists('factory_categories', 'id'),
                Rule::notIn([$factoryCategory->id]), // cannot be its own parent
            ],
            'name' => ['required', 'string', 'max:255', Rule::unique('factory_categories', 'name')->ignore($factoryCategory->id)],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        // Prevent circular parenting: you cannot set a descendant as your parent.
        if (!empty($data['factory_category_id'])) {
            $newParentId = (int) $data['factory_category_id'];
            if ($this->isDescendantOf($newParentId, $factoryCategory->id)) {
                return back()
                    ->withErrors(['factory_category_id' => 'Invalid parent selected (would create a cycle).'])
                    ->withInput();
            }
        }

        $factoryCategory->update([
            'factory_category_id' => $data['factory_category_id'] ?? null,
            'name' => $data['name'],
            'position' => $data['position'] ?? 0,
            'updated_by' => $request->user()?->id,
        ]);

        return back()->with('success', 'Category updated.');
    }

    /**
     * Back-compat alias for updating a child; delegates to update().
     */
    public function updateChild(Request $request, FactoryCategory $factorySubcategory)
    {
        return $this->update($request, $factorySubcategory);
    }

    /**
     * Delete a category and its direct children in a transaction.
     * If you have DB-level ON DELETE CASCADE, the explicit children delete
     * is redundant but harmless.
     */
    public function destroy(FactoryCategory $factoryCategory)
    {
        DB::transaction(function () use ($factoryCategory) {
            // delete direct children first (single level)
            FactoryCategory::where('factory_category_id', $factoryCategory->id)->delete();
            $factoryCategory->delete();
        });

        return back()->with('success', 'Category deleted.');
    }

    /**
     * Delete a single subcategory (back-compat route).
     */
    public function destroyChild(FactoryCategory $factorySubcategory)
    {
        $factorySubcategory->delete();

        return back()->with('success', 'Subcategory deleted.');
    }

    /**
     * Helper: determine if $candidateId is a descendant of $ancestorId.
     * (Prevents circular references on re-parenting.)
     */
    protected function isDescendantOf(int $candidateId, int $ancestorId): bool
    {
        $current = FactoryCategory::find($candidateId);
        while ($current && $current->factory_category_id) {
            if ((int) $current->factory_category_id === $ancestorId) {
                return true;
            }
            $current = FactoryCategory::find($current->factory_category_id);
        }
        return false;
    }
}