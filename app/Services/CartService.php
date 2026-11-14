<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function items(User $user): Collection
    {
        return $user->cart?->items()->with('product')->orderBy('id')->get() ?? collect();
    }

    public function change(User $user, int $quantity, ?int $productId = null, ?int $itemId = null): void
    {
        DB::transaction(function () use ($user, $quantity, $productId, $itemId) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $cart = $itemId === null ? $user->cart()->firstOrCreate() : $user->cart()->firstOrFail();
            if ($itemId !== null) {
                $item = $cart->items()->findOrFail($itemId);
                if ($quantity === 0) {
                    $item->delete();

                    return;
                }
                $productId = $item->product_id;
            } else {
                $item = $cart->items()->firstOrNew(['product_id' => $productId]);
                $quantity += $item->quantity ?? 0;
            }

            $product = Product::whereKey($productId)->lockForUpdate()->first();
            if (! $product?->is_active || $quantity > $product->stock) {
                throw ValidationException::withMessages(['quantity' => 'Produk tidak tersedia atau jumlah melebihi stok terbaru.']);
            }

            $item->quantity = $quantity;
            $item->save();
        }, 3);
    }
}
