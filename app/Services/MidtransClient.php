<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransClient
{
    public function configured(): bool
    {
        return ! config('midtrans.is_production') && filled(config('midtrans.server_key'));
    }

    public function create(Payment $payment): array
    {
        $order = $payment->order()->with('items')->firstOrFail();
        $items = $order->items->map(fn ($item) => [
            'id' => (string) $item->id, 'name' => mb_substr($item->product_name, 0, 50),
            'price' => $item->unit_price, 'quantity' => $item->quantity,
        ])->all();
        if ($order->shipping_cost > 0) {
            $items[] = ['id' => 'SHIPPING', 'name' => 'Ongkos kirim', 'price' => $order->shipping_cost, 'quantity' => 1];
        }

        if ($order->total_amount !== $order->subtotal + $order->shipping_cost
            || $payment->gross_amount !== $order->total_amount
            || array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $items)) !== $order->total_amount
            || $order->total_amount <= 0) {
            throw new RuntimeException('Invalid order snapshot.');
        }

        $result = $this->request()->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
            'transaction_details' => ['order_id' => $payment->midtrans_order_id, 'gross_amount' => $order->total_amount],
            'item_details' => $items,
            'expiry' => [
                'start_time' => $payment->created_at->copy()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => config('midtrans.expiry_minutes'),
            ],
            'callbacks' => ['finish' => route('customer.payment.show', $order)],
        ]);

        $token = $result->json('token');
        $url = $result->json('redirect_url');
        if (! $result->successful() || ! is_string($token) || $token === '' || ! is_string($url)
            || ! str_starts_with($url, 'https://app.sandbox.midtrans.com/snap/')) {
            throw new RuntimeException('Snap initialization could not be confirmed.');
        }

        return ['snap_token' => $token, 'snap_redirect_url' => $url];
    }

    public function status(Payment $payment): ?array
    {
        $result = $this->request()->get('https://api.sandbox.midtrans.com/v2/'.rawurlencode($payment->midtrans_order_id).'/status');
        if ($result->status() === 404 || ($result->successful() && (string) $result->json('status_code') === '404')) {
            return null;
        }
        if (! $result->successful() || ! is_array($result->json())) {
            throw new RuntimeException('Midtrans status unavailable.');
        }

        return $result->json();
    }

    private function request(): PendingRequest
    {
        if (! $this->configured()) {
            throw new RuntimeException('Midtrans Sandbox unavailable.');
        }

        return Http::acceptJson()->withBasicAuth(config('midtrans.server_key'), '')
            ->withoutRedirecting()->connectTimeout(3)->timeout(8);
    }
}
