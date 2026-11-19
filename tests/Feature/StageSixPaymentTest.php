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

    public function test_webhook_validates_signature_using_signature_secret_key(): void
    {
        config(['midtrans.signature_key' => 'dedicated-signature-secret-key']);
        $order = $this->prepareOrder();
        $this->respond($order, 'settlement');

        $amount = '410000.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'gross_amount' => $amount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'dedicated-signature-secret-key'),
        ];

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('processing', $order->fresh()->order_status);
    }

    public function test_dana_bi_snap_payment_reconciliation_with_transaction_id_and_settlement(): void
    {
        $order = $this->prepareOrder();
        $danaTxId = 'A120261119-DANA-BI-SNAP-UUID';

        // Mock Midtrans Status API for DANA (where order_id matches transaction_id)
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/'.$danaTxId.'/status' => Http::response([
                'status_code' => '200',
                'order_id' => $danaTxId,
                'transaction_id' => $danaTxId,
                'gross_amount' => '410000.00',
                'currency' => 'IDR',
                'payment_type' => 'dana',
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
            ]),
        ]);

        $amount = '410000.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_id' => $danaTxId,
            'payment_type' => 'dana',
            'transaction_status' => 'settlement',
            'gross_amount' => $amount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'sandbox-test-key'),
        ];

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

        $order->refresh();
        $payment = $order->payments()->first();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->order_status);
        $this->assertSame('committed', $order->stock_reservation_status);
        $this->assertSame('paid', $payment->internal_status);
        $this->assertSame('settlement', $payment->transaction_status);
        $this->assertSame($danaTxId, $payment->midtrans_transaction_id);
        $this->assertSame('dana', $payment->payment_type);
    }

    public function test_manual_refresh_reconciles_and_redirects_back_with_flash_message(): void
    {
        $order = $this->prepareOrder();
        $refreshUrl = route('customer.payment.refresh', $order);

        // Guest redirect to login
        $this->post($refreshUrl)->assertRedirect(route('login'));

        // Other customer 404
        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->post($refreshUrl)->assertNotFound();

        // Owner customer: pending status before payment
        $this->actingAs($this->customer)
            ->from(route('customer.payment.show', $order))
            ->post($refreshUrl)
            ->assertRedirect(route('customer.payment.show', $order))
            ->assertSessionHas('info', 'Menunggu pembayaran terverifikasi.');

        // Midtrans confirms settlement
        $this->respond($order, 'settlement');

        $this->actingAs($this->customer)
            ->from(route('customer.payment.show', $order))
            ->post($refreshUrl)
            ->assertRedirect(route('customer.payment.show', $order))
            ->assertSessionHas('info', 'Pembayaran terverifikasi.');

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->order_status);
    }

    public function test_virtual_account_webhook_and_status_reconciliation_is_idempotent(): void
    {
        $order = $this->prepareOrder();
        $vaTxId = 'bca-va-transaction-uuid-999';

        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/*' => Http::response([
                'status_code' => '200',
                'order_id' => $order->order_number,
                'transaction_id' => $vaTxId,
                'gross_amount' => '410000.00',
                'currency' => 'IDR',
                'payment_type' => 'bank_transfer',
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
            ]),
        ]);

        $amount = '410000.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_id' => $vaTxId,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'gross_amount' => $amount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'sandbox-test-key'),
        ];

        // First webhook invocation
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, $this->product->fresh()->stock);

        // Duplicate webhook retry
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, $this->product->fresh()->stock);
    }

    public function test_webhook_with_mismatched_amount_does_not_mutate_or_store_transaction_id(): void
    {
        Http::fake();
        $order = $this->prepareOrder();
        $payment = $order->payments()->first();
        $this->assertNull($payment->midtrans_transaction_id);

        $wrongAmount = '999999.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_id' => 'unverified-tx-id-from-webhook',
            'payment_type' => 'dana',
            'transaction_status' => 'settlement',
            'gross_amount' => $wrongAmount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$wrongAmount.'sandbox-test-key'),
        ];

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertUnprocessable();

        // Transaction ID must NOT be stored when amount mismatch occurs
        $this->assertNull($payment->fresh()->midtrans_transaction_id);
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('reserved', $order->fresh()->stock_reservation_status);
        Http::assertNothingSent();
    }

    public function test_reconciliation_is_performed_once_per_webhook_request(): void
    {
        $order = $this->prepareOrder();
        $txId = 'single-reconcile-tx-id';

        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/'.$txId.'/status' => Http::response([
                'status_code' => '200',
                'order_id' => $order->order_number,
                'transaction_id' => $txId,
                'gross_amount' => '410000.00',
                'currency' => 'IDR',
                'payment_type' => 'qris',
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
            ]),
        ]);

        $amount = '410000.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_id' => $txId,
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
            'gross_amount' => $amount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'sandbox-test-key'),
        ];

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

        // Ensure status API was queried exactly once
        $statusCalls = Http::recorded(fn (Request $request) => str_contains($request->url(), '/status') && $request->method() === 'GET');
        $this->assertCount(1, $statusCalls);
        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_different_transaction_id_from_stored_transaction_is_rejected(): void
    {
        $order = $this->prepareOrder();
        $payment = $order->payments()->first();
        $payment->update(['midtrans_transaction_id' => 'legitimate-stored-tx-id']);

        $attackerTxId = 'tampered-or-different-tx-id';
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/'.$attackerTxId.'/status' => Http::response([
                'status_code' => '200',
                'order_id' => $order->order_number,
                'transaction_id' => $attackerTxId,
                'gross_amount' => '410000.00',
                'currency' => 'IDR',
                'payment_type' => 'qris',
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
            ]),
        ]);

        $amount = '410000.00';
        $payload = [
            'order_id' => $order->order_number,
            'transaction_id' => $attackerTxId,
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
            'gross_amount' => $amount,
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number.'200'.$amount.'sandbox-test-key'),
        ];

        // Should fail authoritative status verification (identity invalid)
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertServiceUnavailable();

        // Stored transaction_id must NOT be overwritten
        $this->assertSame('legitimate-stored-tx-id', $payment->fresh()->midtrans_transaction_id);
        $this->assertSame('review', $order->fresh()->payment_status);
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
