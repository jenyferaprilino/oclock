<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->restrictOnDelete();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('type', 20)->default('city'); // city | regency
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
