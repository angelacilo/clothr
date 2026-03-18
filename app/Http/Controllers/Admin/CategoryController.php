<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(20);
        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        $slug = Str::slug($request->name);
        Category::create(['name' => $request->name, 'slug' => $slug]);
        return back()->with('success', 'Category created!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate(['name' => 'required']);
        $slug = Str::slug($request->name);
        $category->update([
            'name' => $request->name, 
            'slug' => $slug, 
            'isVisible' => $request->has('isVisible')
        ]);
        return back()->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // RULE 3: CATEGORY DELETE check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category because it has products associated with it.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted!');
    }
}
