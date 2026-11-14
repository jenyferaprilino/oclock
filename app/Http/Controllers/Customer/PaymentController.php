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

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->with(['items', 'payments'])->withExists('payments')->latest('id')->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    public function show(Request $request, Order $order, PaymentService $payments, MidtransClient $midtrans): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $payment = $order->payments()->oldest('id')->firstOrFail();

        // Otomatis verifikasi status resmi Midtrans saat halaman dimuat
        if (in_array($order->payment_status, ['pending', 'review'], true) && $midtrans->configured()) {
            $payments->reconcile($payment);
            $order->refresh();
            $payment->refresh();
        }

        $isProduction = (bool) config('midtrans.is_production');

        return view('customer.payment', compact('order', 'payment', 'isProduction'));
    }

    public function status(Request $request, Order $order, PaymentService $payments, MidtransClient $midtrans): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $payment = $order->payments()->oldest('id')->firstOrFail();

        // Rekonsiliasi live via API Midtrans jika status masih pending/review
        if (in_array($order->payment_status, ['pending', 'review'], true) && $midtrans->configured()) {
            $payments->reconcile($payment);
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

    public function invoice(Request $request, Order $order): View
    {
        // Ensure the authenticated customer owns the order and it is paid
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->payment_status === 'paid', 403);

        // Load order items (snapshot already eager‑loaded in index)
        $order->load('items');

        return view('customer.invoice', compact('order'));
    }
}
