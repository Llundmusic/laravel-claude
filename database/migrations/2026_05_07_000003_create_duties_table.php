<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duties', function (Blueprint $table) {
            $table->id();
            $table->string('factory');
            $table->string('sales_region');
            $table->decimal('duty_rate', 6, 4)->nullable();
            $table->timestamps();
            $table->unique(['factory', 'sales_region']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duties');
    }
};
