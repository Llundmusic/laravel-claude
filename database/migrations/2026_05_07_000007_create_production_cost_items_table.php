<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_calculation_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('description')->nullable();
            $table->decimal('direct_hours', 10, 2)->nullable();
            $table->decimal('direct_rate', 10, 4)->nullable();
            $table->string('currency')->default('EUR');
            $table->decimal('benchmark_hours', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_cost_items');
    }
};
