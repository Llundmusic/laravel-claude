<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_calculation_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('description')->nullable();
            $table->decimal('qty', 10, 4)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->string('currency')->default('EUR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
