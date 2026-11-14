<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Models\Product;
use App\Models\Province;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageOneFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles_can_be_distinguished(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => 'password',
            'role' => 'customer',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCustomer());
        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->isAdmin());
    }

    public function test_shipping_rate_relates_to_city_and_province(): void
    {
        $province = Province::create([
            'code' => '17',
            'name' => 'Bengkulu',
        ]);

        $city = City::create([
            'province_id' => $province->id,
            'code' => '1771',
            'name' => 'Bengkulu',
            'type' => 'city',
        ]);

        $rate = ShippingRate::create([
            'city_id' => $city->id,
            'price' => 10000,
            'is_active' => true,
        ]);

        $this->assertEquals(10000, $city->shippingRate->price);
        $this->assertEquals('Bengkulu', $rate->city->province->name);
        $this->assertEquals('Kota Bengkulu', $city->full_name);
    }

    public function test_product_category_relation_and_soft_delete(): void
    {
        $category = Category::create([
            'name' => 'Casual',
            'slug' => 'casual',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Watch',
            'slug' => 'test-watch',
            'sku' => 'TEST-001',
            'brand' => "O'Clock",
            'description' => 'A minimalist watch.',
            'price' => 500000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->assertEquals('Casual', $product->category->name);
        $this->assertTrue($product->isAvailable());

        // Test SoftDeletes
        $product->delete();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertNull(Product::find($product->id));
        $this->assertNotNull(Product::withTrashed()->find($product->id));
    }

    public function test_order_and_payment_structure_and_snapshot_integrity(): void
    {
        $user = User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'customer',
        ]);

        $province = Province::create(['code' => '17', 'name' => 'Bengkulu']);
        $city = City::create([
            'province_id' => $province->id,
            'code' => '1771',
            'name' => 'Bengkulu',
            'type' => 'city',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20261113-0001',
            'checkout_key' => 'chk_test_key_123',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
            'province_id' => $province->id,
            'city_id' => $city->id,
            'province_name' => 'Bengkulu',
            'city_name' => 'Bengkulu',
            'district_name' => 'Ratu Agung',
            'shipping_address' => 'Jl. Meranti No. 10',
            'postal_code' => '38228',
            'subtotal' => 450000,
            'shipping_cost' => 10000,
            'total_amount' => 460000,
            'payment_status' => 'pending',
            'order_status' => 'awaiting_payment',
            'stock_reservation_status' => 'reserved',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Aethel Minimalist Watch',
            'product_sku' => 'OC-CS-001',
            'unit_price' => 450000,
            'quantity' => 1,
            'subtotal' => 450000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'PAY-20261113-0001',
            'internal_status' => 'pending',
            'gross_amount' => 460000,
        ]);

        $notif = PaymentNotification::create([
            'payment_id' => $payment->id,
            'midtrans_order_id' => $payment->midtrans_order_id,
            'transaction_status' => 'settlement',
            'signature_valid' => true,
            'payload' => ['status_code' => '200'],
            'processing_status' => 'received',
        ]);

        $this->assertEquals(1, $order->items->count());
        $this->assertEquals(460000, $order->payments->first()->gross_amount);
        $this->assertEquals('Bengkulu', $order->province_name);
        $this->assertTrue($notif->signature_valid);
    }
}
