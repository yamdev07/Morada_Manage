<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Catégories de la carte du restaurant.
 */
class RestaurantCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menus')->ordered()->get();

        return view('restaurant.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? Category::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($id)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? $category->sort_order,
        ]);

        return back()->with('success', 'Catégorie mise à jour.');
    }

    /**
     * Les plats de la catégorie ne sont pas supprimés : ils repassent
     * simplement en « sans catégorie » (contrainte nullOnDelete).
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return back()->with('success', 'Catégorie « '.$name.' » supprimée.');
    }
}
