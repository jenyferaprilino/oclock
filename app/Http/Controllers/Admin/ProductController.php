<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\UpdateStockRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image']);

        $product = Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $updatedProduct = DB::transaction(function () use ($request, $product) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $this->checkStockSnapshot($request, $locked);

            return $this->updateLockedProduct($request, $locked);
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$updatedProduct->name}' berhasil diperbarui.");
    }

    private function updateLockedProduct(UpdateProductRequest $request, Product $product): Product
    {
        $data = $request->safe()->except(['expected_stock']);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('products', 'public');
            // Hapus gambar lama jika ada
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $newPath;
        }

        unset($data['image']);

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        // Soft delete sesuai PRD Bab 7.3 & BR-20
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$name}' berhasil dinonaktifkan / dihapus sementara (soft delete).");
    }

    public function updateStock(UpdateStockRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $this->checkStockSnapshot($request, $lockedProduct);
            $lockedProduct->update([
                'stock' => $request->validated('stock'),
            ]);
        });

        return back()->with('success', "Stok produk '{$product->name}' berhasil diperbarui menjadi {$request->validated('stock')} unit.");
    }

    private function checkStockSnapshot(Request $request, Product $product): void
    {
        if ($request->integer('expected_stock', -1) !== $product->stock) {
            throw ValidationException::withMessages(['stock' => 'Stok telah berubah. Muat ulang halaman sebelum menyimpan.']);
        }
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $statusText = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Produk '{$product->name}' berhasil {$statusText}.");
    }
}
