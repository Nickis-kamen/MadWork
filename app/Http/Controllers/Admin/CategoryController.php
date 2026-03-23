<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::latest()->get();
        return Inertia::render('Admin/Categories', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $slug = Str::slug($request->title);

        if(Category::where('slug', $slug)->exists()) {
            return redirect('/admin/categories')->with([
                'message' => 'Une catégorie avec ce titre existe déjà !',
                'type' => 'error' // ou 'error', 'warning', 'info'
            ]);
        }

        Category::create([
            'title' => $request->title,
            'slug' => $slug
        ]);

        return redirect('/admin/categories')->with([
            'message' => 'Catégorie créée avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $category->id
        ]);

        $category->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title)
        ]);

        return redirect()->back()->with([
            'message' => 'Catégorie modifiée avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with([
            'message' => 'Catégorie supprimée avec succès !',
            'type' => 'success' // ou 'error', 'warning', 'info'
        ]);
    }
}
