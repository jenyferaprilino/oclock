<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan Halaman Utama (CUS-01 Homepage).
     * Menampilkan Hero O'Clock, showcase Kategori, dan Produk Unggulan terbaru.
     */
    public function index(): View
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $showcaseProduct = Product::where('is_active', true)
            ->where('slug', 'alexandre-christie-ac-9601-mar-open-heart')
            ->first();

        return view('home', compact('categories', 'featuredProducts', 'showcaseProduct'));
    }
}
