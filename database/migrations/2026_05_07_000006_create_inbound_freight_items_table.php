<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_freight_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_calculation_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('description')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('currency')->default('EUR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_freight_items');
    }
};
