<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request, CartService $cart, MidtransClient $midtrans): View|RedirectResponse
    {
        $items = $cart->items($request->user());
        if ($items->isEmpty()) {
            return redirect()->route('customer.cart.index')->withErrors(['cart' => 'Keranjang Anda masih kosong.']);
        }

        $provinces = Province::with(['cities' => fn ($query) => $query->orderBy('name')])->orderBy('name')->get();

        return view('customer.checkout', [
            'items' => $items,
            'provinces' => $provinces,
            'cities' => $provinces->flatMap(fn ($province) => $province->cities->map(fn ($city) => [
                'id' => $city->id, 'province_id' => $province->id, 'name' => $city->full_name,
            ])),
            'checkoutKey' => (string) Str::uuid(),
            'paymentEnabled' => config('midtrans.checkout_enabled') && $midtrans->configured(),
        ]);
    }

    public function quote(Request $request, CartService $cart, CheckoutService $checkout): JsonResponse
    {
        $data = $request->validate(['province_id' => 'required|integer', 'city_id' => 'required|integer']);

        return response()->json($checkout->quote($cart->items($request->user()), $data['province_id'], $data['city_id']));
    }

    public function store(Request $request, CheckoutService $checkout, MidtransClient $midtrans, PaymentService $payments): RedirectResponse
    {
        $sandboxEnabled = config('midtrans.checkout_enabled') && $midtrans->configured();
        abort_unless($sandboxEnabled || (app()->environment('testing') && config('checkout.allow_test_orders')), 403, 'Konfirmasi pesanan belum tersedia.');

        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => ['required', 'string', 'regex:/^\\+?[0-9]{7,19}$/'],
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:1000',
            'postal_code' => 'required|digits:5',
            'notes' => 'nullable|string|max:1000',
            'checkout_key' => 'required|uuid',
            'quote' => 'required|size:64|string',
        ]);
        if ($sandboxEnabled) {
            $payment = DB::transaction(fn () => $payments->prepare($checkout->reserve($request->user(), $data)), 3);
            $payments->initialize($payment);

            $snapUrl = $payment->fresh()->snap_redirect_url;
            if (is_string($snapUrl) && str_starts_with($snapUrl, 'https://app.sandbox.midtrans.com/snap/')) {
                return redirect()->away($snapUrl);
            }

            return redirect()->route('customer.payment.show', $payment->order_id);
        }

        $order = $checkout->reserve($request->user(), $data);

        return redirect()->route('customer.cart.index')->with('success', "Pesanan uji {$order->order_number} dibuat.");
    }
}
