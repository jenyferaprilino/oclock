<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Tampilkan Halaman Katalog Jam Tangan (CUS-02).
     * Mendukung pencarian teks (nama, brand, SKU), filter kategori, pagination, dan status stok.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category')
            ->where('is_active', true);

        // Pencarian nama produk, brand, atau SKU
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Pengurutan harga / terbaru
        if ($request->input('sort') === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $selectedCategory = $request->filled('category')
            ? Category::where('slug', $request->input('category'))->first()
            : null;

        return view('catalog.index', compact('products', 'categories', 'selectedCategory'));
    }

    /**
     * Tampilkan Halaman Detail Produk Jam Tangan (CUS-03).
     * Menampilkan spesifikasi fisik lengkap, stok, harga, dan input kuantitas.
     */
    public function show(string $slug): View
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Produk terkait dalam kategori yang sama
        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }
}
