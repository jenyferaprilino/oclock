<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class CustomerInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** Helper to create an order with items */
    private function createOrder(User $user, array $overrides = []): Order
    {
        $province = \App\Models\Province::first() ?? \App\Models\Province::create(['name' => 'Test Province', 'code' => 'TP']);
        $city = $province->cities()->first() ?? $province->cities()->create(['name' => 'Test City', 'code' => 'TC', 'type' => 'city']);

        $default = [
            'user_id' => $user->id,
            'order_number' => 'ORD-'.now()->timestamp,
            'checkout_key' => (string) \Illuminate\Support\Str::uuid(),
            'recipient_name' => 'John Doe',
            'recipient_phone' => '08123456789',
            'province_id' => $province->id,
            'city_id' => $city->id,
            'province_name' => $province->name,
            'city_name' => $city->name,
            'district_name' => 'Test District',
            'shipping_address' => 'Jl. Test 123',
            'postal_code' => '12345',
            'payment_status' => 'paid',
            'order_status' => 'shipped',
            'subtotal' => 1000,
            'shipping_cost' => 100,
            'total_amount' => 1100,
            'courier_name' => 'JNE',
            'tracking_number' => 'OC-TEST123',
        ];

        $order = Order::create(array_merge($default, $overrides));

        // attach one simple item for snapshot
        $order->items()->create([
            'product_name' => 'Test Watch',
            'product_sku' => 'TW-001',
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        return $order;
    }

    public function test_customer_can_view_invoice_for_paid_order()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->createOrder($customer);

        $this->actingAs($customer)
            ->get(route('customer.orders.invoice', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Test Watch')
            ->assertSee('JNE')
            ->assertSee('OC-TEST123');
    }

    public function test_customer_cannot_view_invoice_for_unpaid_order()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->createOrder($customer, ['payment_status' => 'pending']);

        $this->actingAs($customer)
            ->get(route('customer.orders.invoice', $order))
            ->assertStatus(403);
    }

    public function test_other_customer_cannot_access_invoice_of_another_user()
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $order = $this->createOrder($owner);

        $this->actingAs($other)
            ->get(route('customer.orders.invoice', $order))
            ->assertNotFound();
    }
}
