<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('midtrans_order_id');
            $table->string('transaction_status');
            $table->boolean('signature_valid')->default(false);
            $table->json('payload');
            $table->enum('processing_status', ['received', 'processed', 'failed', 'skipped'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('midtrans_order_id');
            $table->index('processing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
    }
};
