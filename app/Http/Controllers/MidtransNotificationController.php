<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransClient $midtrans, PaymentService $payments): JsonResponse
    {
        abort_unless($request->isJson(), 415);
        abort_unless($midtrans->configured(), 503);
        $data = $request->validate([
            'order_id' => 'required|string|max:50',
            'status_code' => 'required|string|digits:3',
            'gross_amount' => ['required', 'string', 'max:30', 'regex:/^\d+(?:\.\d{2})?$/D'],
            'transaction_status' => 'required|string|max:80',
            'signature_key' => ['required', 'string', 'regex:/^[a-f0-9]{128}$/D'],
        ]);

        $signature = hash('sha512', $data['order_id'].$data['status_code'].$data['gross_amount'].config('midtrans.server_key'));
        abort_unless(hash_equals($signature, $data['signature_key']), 401);
        $payment = Payment::where('midtrans_order_id', $data['order_id'])->first();
        $amountValid = $payment && $payments->amountMatches($data['gross_amount'], $payment->gross_amount);
        $processed = $amountValid && $payments->reconcile($payment);

        PaymentNotification::create([
            'payment_id' => $payment?->id,
            'midtrans_order_id' => $data['order_id'],
            'transaction_status' => $data['transaction_status'],
            'signature_valid' => true,
            'payload' => collect($data)->except('signature_key')->all(),
            'processing_status' => $processed ? 'processed' : 'failed',
            'error_message' => $processed ? null : match (true) {
                ! $payment => 'Unknown payment ID.',
                ! $amountValid => 'Gross amount mismatch.',
                default => 'Authoritative status not verified.',
            },
            'processed_at' => now(),
        ]);

        return response()->json(['processed' => $processed], match (true) {
            ! $payment => 404,
            ! $amountValid => 422,
            ! $processed => 503,
            default => 200,
        });
    }
}
