<?php

/**
 * FILE: Admin/CategoryController.php
 * 
 * What this file does:
 * This controller manages the product categories (like "Men", "Women", "Sale").
 * It allows the admin to see all categories, add new ones, update names, 
 * and delete them if they are no longer needed.
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the Category model to talk to the database.
 * - The views it returns are in resources/views/admin/categories.blade.php.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Shows the categories management page.
     * 
     * @return view — the categories list page
     */
    public function index()
    {
        // Get categories from the database, 20 per page.
        $categories = Category::paginate(20);
        return view('admin.categories', compact('categories'));
    }

    /**
     * Saves a new category.
     * 
     * @param Request $request — contains the name of the category
     * @return redirect — back to the categories list
     */
    public function store(Request $request)
    {
        // Ensure the name is not empty.
        $request->validate(['name' => 'required']);
        
        // "slug" is a URL-friendly version of the name.
        // Example: "T-Shirts & Tops" becomes "t-shirts-tops".
        $slug = Str::slug($request->name);
        
        // Save to the database.
        Category::create(['name' => $request->name, 'slug' => $slug]);
        
        return back()->with('success', 'Category created!');
    }

    /**
     * Updates an existing category.
     * 
     * @param Request $request — contains updated name and visibility
     * @param int $id — the ID of the category to update
     * @return redirect — back to the categories list
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        // Ensure the updated name is not empty.
        $request->validate(['name' => 'required']);
        
        // Regenerate the URL slug in case the name changed.
        $slug = Str::slug($request->name);
        
        // Save the updates. 
        // "isVisible" is a checkbox; if it's checked, it appears in the request.
        $category->update([
            'name' => $request->name, 
            'slug' => $slug, 
            'isVisible' => $request->has('isVisible')
        ]);
        
        return back()->with('success', 'Category updated!');
    }

    /**
     * Deletes a category.
     * 
     * @param int $id — the ID of the category to delete
     * @return redirect — back to the categories list
     */
    public function destroy($id)
    {
        // Find the category by its ID.
        $category = Category::findOrFail($id);
        
        // Delete it from the table.
        $category->delete();
        
        return back()->with('success', 'Category deleted!');
    }
}
