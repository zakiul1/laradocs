<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->get('scope', 'factory');       // default scope
        $roots = Category::scope($scope)->roots()->with('children')->orderBy('position')->get();
        return view('admin.categories.index', compact('scope', 'roots'));
    }

    public function storeRoot(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255']
        ]);
        Category::firstOrCreate(['scope' => $data['scope'], 'name' => $data['name'], 'parent_id' => null]);
        return back()->with('status', 'Root category added.');
    }

    public function storeChild(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', 'string', 'max:50'],
            'parent_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);
        Category::firstOrCreate(['scope' => $data['scope'], 'parent_id' => $data['parent_id'], 'name' => $data['name']]);
        return back()->with('status', 'Subcategory added.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }
}