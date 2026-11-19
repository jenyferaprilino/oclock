<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Customer Payment related actions.
 */
class PaymentController extends Controller
{
    /**
     * List customer's orders.
     */
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'payments'])
            ->withExists('payments')
            ->latest('id')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show payment page for a given order.
     */
    public function show(Request $request, Order $order, PaymentService $payments, MidtransClient $midtrans): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $payment = $order->payments()->oldest('id')->firstOrFail();

        // Auto‑reconcile pending/review payments with Midtrans.
        if (in_array($order->payment_status, ['pending', 'review'], true) && $midtrans->configured()) {
            $txId = $request->query('transaction_id');
            $payments->reconcile($payment, transactionId: $txId);
            $order->refresh();
            $payment->refresh();
        }

        $isProduction = (bool) config('midtrans.is_production');

        return view('customer.payment', compact('order', 'payment', 'isProduction'));
    }

    /**
     * Return JSON status for a given order.
     */
    public function status(Request $request, Order $order, PaymentService $payments, MidtransClient $midtrans): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $payment = $order->payments()->oldest('id')->firstOrFail();

        // Auto‑reconcile if needed.
        if (in_array($order->payment_status, ['pending', 'review'], true) && $midtrans->configured()) {
            $txId = $request->query('transaction_id');
            $payments->reconcile($payment, transactionId: $txId);
            $order->refresh();
            $payment->refresh();
        }

        return response()->json([
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'is_paid' => $order->payment_status === 'paid',
            'is_terminal' => in_array($order->payment_status, ['paid', 'failed', 'expired', 'canceled'], true),
            'status_label' => match ($order->payment_status) {
                'paid' => 'Pembayaran terverifikasi.',
                'failed' => 'Pembayaran gagal. Silakan buat pesanan baru.',
                'expired' => 'Pembayaran kedaluwarsa. Silakan buat pesanan baru.',
                'canceled' => 'Pembayaran dibatalkan.',
                'review' => 'Status pembayaran perlu diperiksa. Hindari pembayaran ulang.',
                default => 'Menunggu pembayaran terverifikasi.',
            },
        ]);
    }

    /**
     * Show printable invoice for a paid order.
     */
    public function invoice(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->payment_status === 'paid', 403);

        $order->load('items');

        return view('customer.invoice', compact('order'));
    }

    /**
     * Manually refresh payment status via form submission.
     * Returns back with a flash info message.
     */
    public function refresh(Request $request, Order $order, PaymentService $payments, MidtransClient $midtrans): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $payment = $order->payments()->oldest('id')->firstOrFail();

        if (in_array($order->payment_status, ['pending', 'review'], true) && $midtrans->configured()) {
            $txId = $request->input('transaction_id') ?: $request->query('transaction_id');
            $payments->reconcile($payment, transactionId: $txId);
            $order->refresh();
            $payment->refresh();
        }

        $statusMessage = match ($order->payment_status) {
            'paid' => 'Pembayaran terverifikasi.',
            'failed' => 'Pembayaran gagal. Silakan buat pesanan baru.',
            'expired' => 'Pembayaran kedaluwarsa. Silakan buat pesanan baru.',
            'canceled' => 'Pembayaran dibatalkan.',
            'review' => 'Status pembayaran perlu diperiksa. Hindari pembayaran ulang.',
            default => 'Menunggu pembayaran terverifikasi.',
        };

        return redirect()->back()->with('info', $statusMessage);
    }
}
