<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->string('checkout_key')->unique();

            // Shipping recipient info
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);

            // Shipping location (FK + snapshot)
            $table->foreignId('province_id')->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->string('province_name'); // snapshot
            $table->string('city_name');     // snapshot
            $table->string('district_name');
            $table->text('shipping_address');
            $table->string('postal_code', 10);
            $table->text('notes')->nullable();

            // Financials
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_cost');
            $table->unsignedBigInteger('total_amount');

            // Status
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'expired', 'canceled', 'review', 'refunded',
            ])->default('pending');
            $table->enum('order_status', [
                'awaiting_payment', 'processing', 'packed', 'shipped', 'completed', 'canceled', 'payment_review',
            ])->default('awaiting_payment');
            $table->enum('stock_reservation_status', [
                'reserved', 'committed', 'released',
            ])->default('reserved');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'payment_status']);
            $table->index('order_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
