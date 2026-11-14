<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class StageSevenOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    /** Helper to create an order with given status */
    private function createOrder(array $attrs = []): Order
    {
        // Ensure required related data exists
        $province = \App\Models\Province::first() ?? \App\Models\Province::create(['name' => 'Test Province', 'code' => 'TP']);
        $city = $province->cities()->first() ?? $province->cities()->create(['name' => 'Test City', 'code' => 'TC', 'type' => 'city']);

        $default = [
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'order_number' => 'ORD-'.now()->timestamp,
            'checkout_key' => (string) \Illuminate\Support\Str::uuid(),
            // Required recipient info (dummy values)
            'recipient_name' => 'John Doe',
            'recipient_phone' => '08123456789',
            'province_id' => $province->id,
            'city_id' => $city->id,
            'province_name' => $province->name,
            'city_name' => $city->name,
            'district_name' => 'Test District',
            'shipping_address' => 'Jl. Test 123',
            'postal_code' => '12345',
            // Core status & pricing fields
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'subtotal' => 1000,
            'shipping_cost' => 100,
            'total_amount' => 1100,
        ];
        return Order::create(array_merge($default, $attrs));
    }

    public function test_admin_can_process_paid_processing_order_into_shipped(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder();

        $payload = [
            'courier_name' => 'JNE',
            'tracking_number' => 'TRK-123',
            'action' => 'process',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'shipped',
            'courier_name' => 'JNE',
            'tracking_number' => 'TRK-123',
        ]);
    }

    public function test_admin_can_complete_shipped_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder(['order_status' => 'shipped']);

        $payload = [
            'action' => 'complete',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'completed',
        ]);
    }

    public function test_customer_and_guest_cannot_access_admin_endpoint(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->createOrder();
        $payload = ['action' => 'process'];

        // Customer
        $this->actingAs($customer)
            ->post(route('admin.orders.status.update', $order), $payload)
            ->assertForbidden();

        // Guest (unauthenticated) – ensure we are logged out
        \Illuminate\Support\Facades\Auth::logout();
        $this->post(route('admin.orders.status.update', $order), $payload)
            ->assertRedirect(route('admin.login'));
    }

    public function test_unpaid_order_cannot_be_processed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder(['payment_status' => 'pending']);
        $payload = [
            'courier_name' => 'JNE',
            'tracking_number' => 'TRK-123',
            'action' => 'process',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload);

        $response->assertForbidden();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'processing',
        ]);
    }

    public function test_invalid_state_transition_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder(['order_status' => 'processing']);
        $payload = ['action' => 'complete'];

        $response = $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'processing',
        ]);
    }

    public function test_repeated_processing_does_not_change_tracking_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder();
        $payload = [
            'courier_name' => 'JNE',
            'tracking_number' => 'TRK-123',
            'action' => 'process',
        ];

        // First processing
        $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload)
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('TRK-123', $order->tracking_number);

        // Second processing attempt (should keep same tracking number)
        $this->actingAs($admin)
            ->post(route('admin.orders.status.update', $order), $payload)
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('TRK-123', $order->tracking_number);
    }
}
