<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with existing products!');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // --- API Methods ---------------------------------------------------------

    /** GET /api/admin/categories */
    public function apiIndex()
    {
        return response()->json(Category::withCount('products')->get());
    }

    /** POST /api/admin/categories */
    public function apiStore(\Illuminate\Http\Request $request)
    {
        $request->validate(['category_name' => 'required|string|max:255|unique:categories,category_name']);
        $category = Category::create(['category_name' => $request->category_name]);
        return response()->json($category, 201);
    }

    /** PUT /api/admin/categories/{category} */
    public function apiUpdate(\Illuminate\Http\Request $request, Category $category)
    {
        $request->validate(['category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id']);
        $category->update(['category_name' => $request->category_name]);
        return response()->json($category);
    }

    /** DELETE /api/admin/categories/{category} */
    public function apiDestroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json(['error' => 'Cannot delete category with existing products.'], 422);
        }
        $category->delete();
        return response()->json(['success' => true]);
    }
}
