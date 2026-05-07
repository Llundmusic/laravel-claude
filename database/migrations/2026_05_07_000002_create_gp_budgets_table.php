<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gp_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('sales_region')->unique();
            $table->decimal('gp_target', 5, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gp_budgets');
    }
};
