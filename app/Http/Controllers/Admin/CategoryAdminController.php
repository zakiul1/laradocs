<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->string('scope')->toString() ?: 'factory';
        $scopes = array_keys(config('categories.scopes', []));

        if (!in_array($scope, $scopes, true)) {
            $scope = 'factory';
        }

        $roots = Category::scope($scope)->roots()
            ->with('children.children')
            ->orderBy('position')
            ->get();

        $allForSelect = Category::flatForSelect($scope);

        return view('admin.categories.index', compact('scope', 'scopes', 'roots', 'allForSelect'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', Rule::in(array_keys(config('categories.scopes', [])))],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        if ($data['parent_id']) {
            $parent = Category::findOrFail($data['parent_id']);
            abort_if($parent->scope !== $data['scope'], 422, 'Parent scope mismatch.');
        }

        Category::firstOrCreate([
            'scope' => $data['scope'],
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('status', 'Category saved.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', 'different:category.id'],
        ]);

        if (!empty($data['parent_id'])) {
            $parent = Category::findOrFail($data['parent_id']);
            abort_if($parent->scope !== $category->scope, 422, 'Parent scope mismatch.');
        }

        $category->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }

    // AJAX quick-create used from module forms


    public function quickCreate(\Illuminate\Http\Request $request)
    {
        // Allow both admin & super_admin here (route middleware already checks)
        $data = $request->validate([
            'scope' => ['required', 'in:factory'], // lock to factory for this UI
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        // If parent is provided, enforce same scope
        if (!empty($data['parent_id'])) {
            $parentScope = \App\Models\Category::where('id', $data['parent_id'])->value('scope');
            if ($parentScope !== $data['scope']) {
                return response()->json(['message' => 'Parent scope mismatch.'], 422);
            }
        }

        // Compute next position within the same parent/scope
        $nextPos = Category::where('scope', $data['scope'])
            ->where(function ($q) use ($data) {
                $pid = $data['parent_id'] ?? null;
                $pid === null ? $q->whereNull('parent_id') : $q->where('parent_id', $pid);
            })
            ->max('position');

        $cat = Category::create([
            'scope' => $data['scope'],
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'position' => (int) $nextPos + 1,
        ]);

        return response()->json([
            'id' => $cat->id,
            'name' => $cat->name,
            'parent_id' => $cat->parent_id,
        ], 201);
    }

}