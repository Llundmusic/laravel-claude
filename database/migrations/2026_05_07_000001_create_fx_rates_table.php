<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fx_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency')->unique();
            $table->decimal('live_rate', 15, 8)->default(1);
            $table->decimal('frozen_rate', 15, 8)->nullable();
            $table->timestamp('live_updated_at')->nullable();
            $table->timestamp('frozen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fx_rates');
    }
};
