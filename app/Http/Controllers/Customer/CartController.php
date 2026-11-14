<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request, CartService $cart): View
    {
        return view('customer.cart', ['items' => $cart->items($request->user())]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate(['product_id' => 'required|integer|min:1', 'quantity' => 'required|integer|min:1|max:2147483647']);
        $cart->change($request->user(), $data['quantity'], productId: $data['product_id']);

        return redirect()->route('customer.cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartService $cart, int $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:2147483647']);
        $cart->change($request->user(), $data['quantity'], itemId: $item);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, CartService $cart, int $item): RedirectResponse
    {
        $cart->change($request->user(), 0, itemId: $item);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
