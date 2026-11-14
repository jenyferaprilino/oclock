<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->enum('internal_status', [
                'pending', 'paid', 'failed', 'expired', 'canceled', 'review', 'refunded',
            ])->default('pending');
            $table->unsignedBigInteger('gross_amount');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('internal_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
