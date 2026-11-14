<?php

namespace App\Services;

use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function quote(Collection $items, int $provinceId, int $cityId, bool $lock = false): array
    {
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Keranjang Anda masih kosong.']);
        }

        $city = City::with('province')->where('province_id', $provinceId)->find($cityId);
        $rateQuery = ShippingRate::active()->where('city_id', $cityId);
        $rate = $city ? ($lock ? $rateQuery->lockForUpdate() : $rateQuery)->first() : null;
        if (! $rate) {
            throw ValidationException::withMessages(['city_id' => 'Kota tidak sesuai dengan provinsi atau tarif pengiriman belum tersedia.']);
        }

        $productsQuery = Product::whereKey($items->pluck('product_id')->all())->orderBy('id');
        $products = ($lock ? $productsQuery->lockForUpdate() : $productsQuery)->get()->keyBy('id');
        $lines = [];
        foreach ($items as $item) {
            $product = $products->get($item->product_id);
            if (! $product?->is_active || $item->quantity < 1 || $item->quantity > $product->stock) {
                throw ValidationException::withMessages(['cart' => 'Stok atau ketersediaan produk berubah. Perbarui keranjang terlebih dahulu.']);
            }

            $lines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => $product->price,
                'quantity' => $item->quantity,
                'subtotal' => $product->price * $item->quantity,
            ];
        }

        $subtotal = array_sum(array_column($lines, 'subtotal'));
        $signature = hash_hmac('sha256', serialize([
            $provinceId, $cityId, $rate->price, $lines,
            $products->map->stock->all(),
        ]), config('app.key'));

        return [
            'items' => $lines,
            'province_name' => $city->province->name,
            'city_name' => $city->full_name,
            'subtotal' => $subtotal,
            'shipping_cost' => $rate->price,
            'total_amount' => $subtotal + $rate->price,
            'quote' => $signature,
        ];
    }

    public function reserve(User $user, array $data): Order
    {
        abort_unless($user->isCustomer(), 403);

        return DB::transaction(function () use ($user, $data) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $key = $user->id.':'.$data['checkout_key'];
            $existing = $user->orders()->where('checkout_key', $key)->first();
            if ($existing) {
                return $existing;
            }

            $cart = $user->cart()->first();
            $items = $cart?->items()->orderBy('id')->get() ?? collect();
            $quote = $this->quote($items, $data['province_id'], $data['city_id'], lock: true);
            if (! hash_equals($quote['quote'], $data['quote'])) {
                throw ValidationException::withMessages(['cart' => 'Harga, ongkir, atau stok berubah. Tinjau ringkasan terbaru.']);
            }

            $order = $user->orders()->create([
                ...collect($data)->only([
                    'recipient_name', 'recipient_phone', 'province_id', 'city_id',
                    'district_name', 'shipping_address', 'postal_code', 'notes',
                ])->all(),
                ...collect($quote)->except(['items', 'quote'])->all(),
                'order_number' => 'OC-'.Str::ulid(),
                'checkout_key' => $key,
                'payment_status' => 'pending',
                'order_status' => 'awaiting_payment',
                'stock_reservation_status' => 'reserved',
            ]);

            $order->items()->createMany($quote['items']);
            foreach ($quote['items'] as $line) {
                Product::whereKey($line['product_id'])->decrement('stock', $line['quantity']);
            }
            $cart->items()->delete();

            return $order;
        }, 3);
    }
}
