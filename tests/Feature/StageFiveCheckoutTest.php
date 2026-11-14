<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class StageFiveCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Product $product;

    private Province $province;

    private City $city;

    private ShippingRate $rate;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.checkout_enabled' => false, 'midtrans.server_key' => null]);

        $this->customer = User::factory()->create(['role' => 'customer']);
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);
        $this->product = Product::create([
            'category_id' => $category->id, 'name' => 'Test Watch', 'slug' => 'test-watch',
            'sku' => 'TEST', 'brand' => 'Test', 'description' => 'Test fixture',
            'price' => 200000, 'stock' => 3, 'is_active' => true,
        ]);
        $this->province = Province::create(['name' => 'Bengkulu', 'code' => '17']);
        $this->city = $this->province->cities()->create(['name' => 'Bengkulu', 'code' => '1771', 'type' => 'city']);
        $this->rate = $this->city->shippingRate()->create(['price' => 10000, 'is_active' => true]);
    }

    public function test_cart_is_customer_only_and_quantities_are_validated(): void
    {
        $payload = ['product_id' => $this->product->id, 'quantity' => 1];
        $this->post(route('customer.cart.store'), $payload)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('customer.cart.store'), $payload)->assertForbidden();

        $this->actingAs($this->customer);
        foreach ([0, 4, 'abc'] as $quantity) {
            $this->postJson(route('customer.cart.store'), [...$payload, 'quantity' => $quantity])
                ->assertUnprocessable()->assertJsonValidationErrors('quantity');
        }
        $this->product->update(['is_active' => false]);
        $this->postJson(route('customer.cart.store'), $payload)->assertJsonValidationErrors('quantity');
        $this->product->update(['is_active' => true]);
        $this->product->delete();
        $this->postJson(route('customer.cart.store'), $payload)->assertJsonValidationErrors('quantity');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_merges_rows_and_scopes_updates_and_deletes_to_owner(): void
    {
        $this->addToCart();
        $this->addToCart();
        $item = $this->customer->cart->items()->firstOrFail();
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertSame(2, $item->quantity);
        $this->assertSame(3, $this->product->fresh()->stock);

        $this->actingAs(User::factory()->create(['role' => 'customer']));
        $this->patch(route('customer.cart.update', $item), ['quantity' => 1])->assertNotFound();
        $this->delete(route('customer.cart.destroy', $item))->assertNotFound();

        $this->actingAs($this->customer)->patch(route('customer.cart.update', $item), ['quantity' => 1])->assertRedirect();
        $this->product->update(['price' => 210000]);
        $this->get(route('customer.cart.index'))->assertOk()->assertSee('Rp210.000');
        $this->assertSame(1, $item->fresh()->quantity);
        $this->delete(route('customer.cart.destroy', $item))->assertRedirect();
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_quote_requires_active_rate_in_matching_province(): void
    {
        $this->addToCart(2);
        $this->get(route('customer.checkout.show'))->assertOk()->assertSee('Alamat Pengiriman');
        $this->postJson(route('customer.checkout.quote'), [
            'province_id' => 99, 'city_id' => $this->city->id,
        ])->assertJsonValidationErrors('city_id');
        $this->assertSame(410000, $this->quote()['total_amount']);

        $this->rate->update(['is_active' => false]);
        $this->postJson(route('customer.checkout.quote'), $this->destination())->assertJsonValidationErrors('city_id');
        $this->rate->delete();
        $this->postJson(route('customer.checkout.quote'), $this->destination())->assertJsonValidationErrors('city_id');
    }

    public function test_public_confirmation_is_blocked_and_test_confirmation_validates_address(): void
    {
        $this->addToCart();
        $this->postJson(route('customer.checkout.store'), $this->orderData())->assertForbidden();
        config(['checkout.allow_test_orders' => true]);
        $this->postJson(route('customer.checkout.store'), [])->assertJsonValidationErrors('recipient_name');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_order_snapshots_and_idempotency_ignore_browser_prices(): void
    {
        config(['checkout.allow_test_orders' => true]);
        $this->addToCart(2);
        $data = $this->orderData();
        $this->post(route('customer.checkout.store'), $data)->assertRedirect(route('customer.cart.index'));
        $this->post(route('customer.checkout.store'), $data)->assertRedirect(route('customer.cart.index'));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(1, $this->product->fresh()->stock);

        $order = Order::sole();
        $this->rate->update(['price' => 20000]);
        $this->product->update(['price' => 500000]);
        $this->assertSame(410000, $order->fresh()->total_amount);
        $this->assertSame(10000, $order->shipping_cost);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('awaiting_payment', $order->order_status);
        $this->assertSame('reserved', $order->stock_reservation_status);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id, 'product_sku' => 'TEST', 'product_name' => 'Test Watch',
            'unit_price' => 200000, 'quantity' => 2, 'subtotal' => 400000,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.orders.show', $order))->assertOk()->assertSee($order->order_number);
    }

    public function test_stale_quotes_and_competing_carts_cannot_overallocate_stock(): void
    {
        config(['checkout.allow_test_orders' => true]);
        $this->addToCart(2);
        $first = $this->customer;
        $firstData = $this->orderData();
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->addToCart(2);
        $secondData = $this->orderData();

        $this->actingAs($first)->post(route('customer.checkout.store'), $firstData)->assertRedirect(route('customer.cart.index'));
        $this->actingAs($this->customer)->postJson(route('customer.checkout.store'), $secondData)->assertJsonValidationErrors('cart');
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, $this->product->fresh()->stock);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->patchJson(route('admin.products.stock', $this->product), ['expected_stock' => 3, 'stock' => 10])
            ->assertJsonValidationErrors('stock');
        $this->assertSame(1, $this->product->fresh()->stock);
    }

    public function test_failed_stock_write_rolls_back_order_items_and_cart_changes(): void
    {
        config(['checkout.allow_test_orders' => true]);
        $this->addToCart();
        $data = $this->orderData();
        Product::getConnectionResolver()->connection()->beforeExecuting(function ($query) {
            if (preg_match('/^update ["`]?products/i', $query)) {
                throw new RuntimeException('Simulated stock write failure');
            }
        });

        $this->postJson(route('customer.checkout.store'), $data)->assertServerError();
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    private function addToCart(int $quantity = 1): void
    {
        $this->actingAs($this->customer)->post(route('customer.cart.store'), [
            'product_id' => $this->product->id, 'quantity' => $quantity,
        ])->assertRedirect(route('customer.cart.index'));
    }

    private function destination(): array
    {
        return ['province_id' => $this->province->id, 'city_id' => $this->city->id];
    }

    private function quote(): array
    {
        return $this->postJson(route('customer.checkout.quote'), $this->destination())->assertOk()->json();
    }

    private function orderData(): array
    {
        return [
            ...$this->destination(),
            'recipient_name' => 'Customer', 'recipient_phone' => '08123456789',
            'district_name' => 'Kecamatan', 'shipping_address' => 'Jalan Uji 1', 'postal_code' => '38111',
            'checkout_key' => (string) Str::uuid(), 'quote' => $this->quote()['quote'],
            'subtotal' => 1, 'shipping_cost' => 1, 'total_amount' => 2,
        ];
    }
}
