<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->withTrashed();
        }])->orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $name = $request->validated('name');
        $slug = $request->validated('slug') ?: Str::slug($name);

        Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil ditambahkan.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $name = $request->validated('name');
        $slug = $request->validated('slug') ?: Str::slug($name);

        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Proteksi PRD Bab 7.2: Kategori yang masih digunakan produk aktif atau nonaktif tidak dapat dihapus
        $productCount = $category->products()->withTrashed()->count();

        if ($productCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$productCount} produk.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$categoryName}' berhasil dihapus.");
    }
}
