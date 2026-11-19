<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(private MidtransClient $midtrans) {}

    public function prepare(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $existing = $locked->payments()->oldest('id')->first();
            if ($existing) {
                return $existing;
            }
            abort_unless($locked->payment_status === 'pending' && $locked->stock_reservation_status === 'reserved', 409);
            $payment = $locked->payments()->create([
                'midtrans_order_id' => $locked->order_number,
                'gross_amount' => $locked->total_amount,
                'internal_status' => 'pending',
                'transaction_status' => 'snap_ready',
            ]);
            $locked->update(['expires_at' => $payment->created_at->copy()->addMinutes(config('midtrans.expiry_minutes'))]);

            return $payment;
        }, 3);
    }

    public function initialize(Payment $payment): void
    {
        // Claim once before the external POST. Retries reconcile the same ID.
        $claimed = Payment::whereKey($payment->id)->where('transaction_status', 'snap_ready')
            ->where('internal_status', 'pending')->update(['transaction_status' => 'snap_initializing']);

        if (! $claimed) {
            if (! $payment->fresh()->snap_token) {
                $this->reconcile($payment);
            }

            return;
        }

        try {
            $snap = $this->midtrans->create($payment);
            Payment::whereKey($payment->id)->where('internal_status', 'pending')->whereNull('snap_token')->update($snap);
            Payment::whereKey($payment->id)->where('transaction_status', 'snap_initializing')
                ->where('internal_status', 'pending')->update(['transaction_status' => 'snap_created']);
        } catch (ConnectionException|RuntimeException) {
            $this->reconcile($payment, markUnknown: true);
        }
    }

    public function reconcile(Payment $payment, bool $markUnknown = false, ?string $transactionId = null): bool
    {
        try {
            $status = $this->midtrans->status($payment, $transactionId);
        } catch (ConnectionException|RuntimeException) {
            $status = null;
        }

        if ($status === null) {
            if ($markUnknown) {
                $this->apply($payment, [], unknown: true);
            }

            return false;
        }

        return $this->apply($payment, $status, transactionIdHint: $transactionId);
    }

    public function amountMatches(mixed $amount, int $expected): bool
    {
        return (is_string($amount) || is_int($amount))
            && preg_match('/\A([0-9]+)(?:\.00)?\z/', (string) $amount, $parts)
            && (ltrim($parts[1], '0') ?: '0') === (string) $expected;
    }

    private function apply(Payment $attempt, array $status, bool $unknown = false, ?string $transactionIdHint = null): bool
    {
        return DB::transaction(function () use ($attempt, $status, $unknown, $transactionIdHint) {
            $order = Order::whereKey($attempt->order_id)->lockForUpdate()->firstOrFail();
            $payment = Payment::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            $transactionId = $status['transaction_id'] ?? null;
            $orderIdMatches = ($status['order_id'] ?? null) === $payment->midtrans_order_id
                || (($status['order_id'] ?? null) === $transactionId
                    && ($payment->midtrans_transaction_id === $transactionId || ($transactionIdHint === $transactionId && in_array($status['payment_type'] ?? null, ['dana'], true))));
            $identityValid = $orderIdMatches
                && ($status['currency'] ?? 'IDR') === 'IDR'
                && $payment->gross_amount === $order->total_amount
                && $this->amountMatches($status['gross_amount'] ?? null, $order->total_amount)
                && is_string($transactionId) && filled($transactionId)
                && strlen($transactionId) <= 255
                && (! $payment->midtrans_transaction_id || $transactionId === $payment->midtrans_transaction_id);
            $transaction = $status['transaction_status'] ?? null;
            $fraud = $status['fraud_status'] ?? null;
            $next = match ($transaction) {
                'settlement' => $fraud === null || $fraud === 'accept' ? 'paid' : 'review',
                'capture' => $fraud === 'accept' ? 'paid' : 'review',
                'pending' => 'pending',
                'deny', 'failure' => 'failed',
                'expire' => 'expired',
                'cancel' => 'canceled',
                default => 'review',
            };

            if ($unknown || ! $identityValid) {
                if (! $payment->isTerminal() && $order->stock_reservation_status === 'reserved') {
                    $this->review($order, $payment);
                }

                return false;
            }

            if ($payment->internal_status === 'paid' || $order->stock_reservation_status === 'committed') {
                if (in_array($transaction, ['refund', 'partial_refund', 'chargeback', 'partial_chargeback'], true)) {
                    $payment->update(['transaction_status' => $transaction]);
                    $order->update(['order_status' => 'payment_review']);
                }

                return true;
            }

            if ($order->stock_reservation_status === 'released') {
                if ($next === 'paid') {
                    $this->review($order, $payment);
                }

                return true;
            }

            if ($payment->internal_status === 'review' && $next === 'pending') {
                return true;
            }

            $payment->update([
                'internal_status' => $next,
                'transaction_status' => is_string($transaction) ? substr($transaction, 0, 80) : null,
                'fraud_status' => is_string($fraud) ? substr($fraud, 0, 80) : null,
                'midtrans_transaction_id' => $transactionId,
                'payment_type' => is_string($status['payment_type'] ?? null) ? substr($status['payment_type'], 0, 80) : null,
            ]);

            if ($next === 'paid') {
                $payment->update(['paid_at' => now()]);
                $order->update(['payment_status' => 'paid', 'order_status' => 'processing', 'stock_reservation_status' => 'committed', 'paid_at' => now()]);
            } elseif (in_array($next, ['failed', 'expired', 'canceled'], true)) {
                if ($order->payments()->whereKeyNot($payment->id)->whereIn('internal_status', ['pending', 'review', 'paid'])->exists()) {
                    $this->review($order, $payment);

                    return false;
                }

                $items = $order->items()->orderBy('product_id')->get();
                if ($items->isEmpty() || $items->contains(fn ($item) => $item->product_id === null)) {
                    $this->review($order, $payment);

                    return false;
                }

                foreach ($items as $item) {
                    Product::withTrashed()->whereKey($item->product_id)->increment('stock', $item->quantity);
                }
                $payment->update([$next === 'expired' ? 'expired_at' : 'failed_at' => now()]);
                $order->update(['payment_status' => $next, 'order_status' => 'canceled', 'stock_reservation_status' => 'released']);
            } elseif ($next === 'review') {
                $this->review($order, $payment);
            }

            return true;
        }, 3);
    }

    private function review(Order $order, Payment $payment): void
    {
        $payment->update(['internal_status' => 'review']);
        $order->update(['payment_status' => 'review', 'order_status' => 'payment_review']);
    }
}
