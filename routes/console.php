<?php

use App\Models\Payment;
use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payments:reconcile', function (PaymentService $payments, MidtransClient $midtrans) {
    if (! $midtrans->configured()) {
        $this->warn('Midtrans Sandbox belum dikonfigurasi.');

        return;
    }

    Payment::query()->whereIn('internal_status', ['pending', 'review'])
        ->whereHas('order', fn ($query) => $query->where('stock_reservation_status', 'reserved')->where('expires_at', '<=', now()))
        ->lazyById(100)
        ->each(fn (Payment $payment) => $payments->reconcile($payment, markUnknown: true));
})->purpose('Memeriksa status Midtrans untuk reservasi yang melewati batas pembayaran');

Schedule::command('payments:reconcile')->everyFiveMinutes()->withoutOverlapping();
