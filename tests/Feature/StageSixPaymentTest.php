<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class StageSixPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Product $product;

    private array $checkout;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => 'sandbox-test-key', 'midtrans.checkout_enabled' => true]);
        Http::preventStrayRequests();
        $this->customer = User::factory()->create(['role' => 'customer']);
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);
        $this->product = Product::create([
            'category_id' => $category->id, 'name' => 'Watch', 'slug' => 'watch',
            'sku' => 'WATCH', 'brand' => 'Watch', 'description' => 'Fixture',
            'price' => 200000, 'stock' => 3, 'is_active' => true,
        ]);
        $province = Province::create(['name' => 'Bengkulu', 'code' => '17']);
        $city = $province->cities()->create(['name' => 'Bengkulu', 'code' => '1771', 'type' => 'city']);
        $city->shippingRate()->create(['price' => 10000, 'is_active' => true]);
        $cart = app(CartService::class);
        $cart->change($this->customer, 2, productId: $this->product->id);
        $quote = app(CheckoutService::class)->quote($cart->items($this->customer), $province->id, $city->id);
        $this->checkout = [
            'province_id' => $province->id, 'city_id' => $city->id,
            'recipient_name' => 'Customer', 'recipient_phone' => '08123456789',
            'district_name' => 'Uji', 'shipping_address' => 'Jalan Uji', 'postal_code' => '38111',
            'checkout_key' => (string) Str::uuid(), 'quote' => $quote['quote'],
            'total_amount' => 1,
        ];
    }

    public function test_checkout_reuses_order_and_snap_and_hides_server_key(): void
    {
        $snapUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/fake-token';
        Http::fake(['https://app.sandbox.midtrans.com/*' => Http::response([
            'token' => 'fake-token', 'redirect_url' => $snapUrl,
        ], 201)]);

        $this->actingAs($this->customer)->post(route('customer.checkout.store'), $this->checkout)->assertRedirect($snapUrl);
        $order = Order::sole();
        $url = route('customer.payment.show', $order);
        $this->post(route('customer.checkout.store'), $this->checkout)->assertRedirect($snapUrl);
        $this->get($url)->assertOk()->assertSee('Buka pembayaran')->assertDontSee('sandbox-test-key');
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, $this->product->fresh()->stock);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request['transaction_details'] === ['order_id' => $order->order_number, 'gross_amount' => 410000]
            && $request['item_details'][1] === ['id' => 'SHIPPING', 'name' => 'Ongkos kirim', 'price' => 10000, 'quantity' => 1]
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sandbox-test-key:'))
        );
        foreach (['customer', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get($url)->assertStatus($role === 'admin' ? 403 : 404);
        }
    }

    public function test_payment_status_polling_endpoint_reconciles_automatically_and_protects_access(): void
    {
        $order = $this->prepareOrder();
        $statusUrl = route('customer.payment.status', $order);

        // Guest redirect to login
        $this->getJson($statusUrl)->assertUnauthorized();

        // Admin forbidden
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->getJson($statusUrl)->assertForbidden();

        // Other customer 404
        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->getJson($statusUrl)->assertNotFound();

        // Owner customer: pending initially
        $this->actingAs($this->customer)->getJson($statusUrl)->assertOk()
            ->assertJson([
                'order_number' => $order->order_number,
                'payment_status' => 'pending',
                'is_paid' => false,
                'is_terminal' => false,
            ]);

        // Midtrans confirms settlement -> automatic reconciliation via status polling
        $this->respond($order, 'settlement');
        $this->getJson($statusUrl)->assertOk()
            ->assertJson([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'is_paid' => true,
                'is_terminal' => true,
                'status_label' => 'Pembayaran terverifikasi.',
            ]);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->order_status);
        $this->assertSame('committed', $order->stock_reservation_status);
        $this->assertSame(1, $this->product->fresh()->stock);
    }

    public function test_payment_page_renders_sandbox_simulator_and_hides_in_production(): void
    {
        $order = $this->prepareOrder();
        $payment = $order->payments()->first();
        $payment->update(['snap_redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/fake']);
        $url = route('customer.payment.show', $order);

        // Sandbox mode (is_production = false)
        config(['midtrans.is_production' => false]);
        $this->actingAs($this->customer)->get($url)->assertOk()
            ->assertSee('Buka Midtrans')
            ->assertSee('id="btn-pay-simulator"', false)
            ->assertSee('Lihat Pesanan Saya')
            ->assertSee('Simulator Resmi Midtrans Sandbox')
            ->assertSee('https://simulator.sandbox.midtrans.com/qris/index');

        // Production mode (is_production = true) -> Simulator must be hidden!
        putenv('MIDTRANS_IS_PRODUCTION=true');
        $_ENV['MIDTRANS_IS_PRODUCTION'] = 'true';
        config(['midtrans.is_production' => true]);

        $this->actingAs($this->customer)->get($url)->assertOk()
            ->assertDontSee('id="btn-pay-simulator"', false)
            ->assertDontSee('Simulator Resmi Midtrans Sandbox')
            ->assertDontSee('https://simulator.sandbox.midtrans.com/qris/index');

        // Restore
        putenv('MIDTRANS_IS_PRODUCTION=false');
        $_ENV['MIDTRANS_IS_PRODUCTION'] = 'false';
        config(['midtrans.is_production' => false]);
    }

    public function test_customer_order_history_only_lists_owned_orders(): void
    {
        $order = $this->prepareOrder();
        $url = route('customer.orders.index');

        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs($this->customer)->get($url)->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Rp410.000')
            ->assertSee(route('customer.payment.show', $order));
        $this->actingAs(User::factory()->create(['role' => 'customer']))->get($url)
            ->assertOk()->assertDontSee($order->order_number);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get($url)->assertForbidden();
    }

    public function test_timeout_reconciles_same_attempt_and_retains_stock_when_status_is_unknown(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/*' => Http::failedConnection(),
            'https://api.sandbox.midtrans.com/*' => Http::response(['status_code' => '404'], 404),
        ]);

        $this->actingAs($this->customer)->post(route('customer.checkout.store'), $this->checkout)->assertRedirect();
        $this->post(route('customer.checkout.store'), $this->checkout)->assertRedirect();
        $order = Order::sole();
        $this->assertSame('review', $order->payment_status);
        $this->assertSame('reserved', $order->stock_reservation_status);
        $this->assertSame(1, $this->product->fresh()->stock);
        $this->assertDatabaseCount('payments', 1);
        $this->assertCount(1, Http::recorded(fn (Request $request) => $request->method() === 'POST'));
    }

    public function test_webhook_rejects_bad_signature_wrong_amount_and_unknown_attempt(): void
    {
        Http::fake();
        $order = $this->prepareOrder();
        $webhook = route('payments.midtrans.notification');
        $this->postJson($webhook, [...$this->notification($order, 'settlement'), 'signature_key' => str_repeat('0', 128)])->assertUnauthorized();
        $this->postJson($webhook, $this->notification($order, 'settlement', '1.00'))->assertUnprocessable();
        $order->order_number = 'UNKNOWN';
        $this->postJson($webhook, $this->notification($order, 'settlement'))->assertNotFound();
        $this->assertDatabaseHas('payment_notifications', ['error_message' => 'Gross amount mismatch.']);
        Http::assertNothingSent();
    }

    public function test_authoritative_status_prevents_out_of_order_webhooks_from_reversing_paid(): void
    {
        $order = $this->prepareOrder();
        $this->respond($order, 'pending');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'settlement'))->assertOk();
        $this->assertSame('pending', $order->fresh()->payment_status);

        $this->respond($order, 'capture', 'challenge');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'capture'))->assertOk();
        $this->assertSame('review', $order->fresh()->payment_status);

        $this->respond($order, 'capture', 'accept');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'capture'))->assertOk();
        $this->respond($order, 'settlement');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'pending'))->assertOk();

        $this->respond($order, 'expire');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'expire'))->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('committed', $order->fresh()->stock_reservation_status);
        $this->assertSame(1, $this->product->fresh()->stock);
    }

    public function test_expiry_reconciliation_releases_once_and_late_settlement_is_reviewed(): void
    {
        $order = $this->prepareOrder();
        $order->update(['expires_at' => now()->subMinute()]);
        $this->respond($order, 'expire');
        $this->artisan('payments:reconcile')->assertSuccessful();
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'expire'))->assertOk();
        $this->assertSame(3, $this->product->fresh()->stock);
        $this->assertSame('expired', $order->fresh()->payment_status);

        $this->respond($order, 'settlement');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'settlement'))->assertOk();
        $this->assertSame('review', $order->fresh()->payment_status);
        $this->assertSame('released', $order->fresh()->stock_reservation_status);
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_failure_releases_once_and_wrong_authoritative_amount_requires_review(): void
    {
        $order = $this->prepareOrder();
        $this->respond($order, 'settlement', amount: '1.00');
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'settlement'))->assertServiceUnavailable();
        $this->assertSame('review', $order->fresh()->payment_status);
        $this->assertSame(1, $this->product->fresh()->stock);

        $this->respond($order, 'deny');
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'deny'))->assertOk();
        }
        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_cancellation_releases_stock_once_and_is_idempotent(): void
    {
        $order = $this->prepareOrder();
        $this->assertSame(1, $this->product->fresh()->stock);

        $this->respond($order, 'cancel');
        // Double webhook invocation or refresh
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'cancel'))->assertOk();
        $this->postJson(route('payments.midtrans.notification'), $this->notification($order, 'cancel'))->assertOk();

        $order->refresh();
        $this->assertSame('canceled', $order->payment_status);
        $this->assertSame('canceled', $order->order_status);
        $this->assertSame('released', $order->stock_reservation_status);
        // Stock must be restored to 3, never doubled to 5
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_scheduler_reconciliation_handles_browser_close_and_releases_stock_after_expiry(): void
    {
        $order = $this->prepareOrder();
        $this->assertSame(1, $this->product->fresh()->stock);

        // Simulate customer closed browser and order expired
        $order->update(['expires_at' => now()->subMinutes(5)]);
        $this->respond($order, 'expire');

        // Scheduler background execution
        $this->artisan('payments:reconcile')->assertSuccessful();

        $order->refresh();
        $this->assertSame('expired', $order->payment_status);
        $this->assertSame('canceled', $order->order_status);
        $this->assertSame('released', $order->stock_reservation_status);
        $this->assertSame(3, $this->product->fresh()->stock);

        // Admin & Customer consistency check
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('EXPIRED')
            ->assertSee('CANCELED')
            ->assertSee('RELEASED');

        $this->actingAs($this->customer)->get(route('customer.orders.index'))
            ->assertOk()
            ->assertSee('Pembayaran kedaluwarsa');
    }

    public function test_scheduler_reconciliation_does_not_release_stock_on_midtrans_404_or_timeout(): void
    {
        $order = $this->prepareOrder();
        $this->assertSame(1, $this->product->fresh()->stock);

        // Order expired in local database
        $order->update(['expires_at' => now()->subMinutes(5)]);

        // Midtrans returns 404 (status not found yet / transaction not completed)
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/*' => Http::response(['status_code' => '404', 'status_message' => 'Transaction not found.'], 404),
        ]);

        $this->artisan('payments:reconcile')->assertSuccessful();

        $order->refresh();
        // Crucial safety check: stock must NOT be released, payment marked for review
        $this->assertSame('review', $order->payment_status);
        $this->assertSame('payment_review', $order->order_status);
        $this->assertSame('reserved', $order->stock_reservation_status);
        $this->assertSame(1, $this->product->fresh()->stock);
    }

    private function prepareOrder(): Order
    {
        $order = app(CheckoutService::class)->reserve($this->customer, $this->checkout);
        app(PaymentService::class)->prepare($order);

        return $order;
    }

    private function notification(Order $order, string $status, string $amount = '410000.00'): array
    {
        return [
            'order_id' => $order->order_number, 'transaction_status' => $status,
            'gross_amount' => $amount, 'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'sandbox-test-key'),
        ];
    }

    private function respond(Order $order, string $status, ?string $fraud = null, string $amount = '410000.00'): void
    {
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/*' => Http::response([
            'order_id' => $order->order_number, 'transaction_status' => $status,
            'gross_amount' => $amount, 'transaction_id' => 'midtrans-fixture',
            'fraud_status' => $fraud, 'payment_type' => 'credit_card', 'currency' => 'IDR',
        ])]);
    }
}
