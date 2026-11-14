<?php

use App\Http\Controllers\MidtransNotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/midtrans/notification', MidtransNotificationController::class)
    ->name('payments.midtrans.notification');
