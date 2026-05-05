<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_country', 10)->nullable();
            $table->string('defined_billing_address_line1')->nullable();
            $table->string('defined_billing_address_line2')->nullable();
            $table->string('defined_billing_postal_code', 20)->nullable();
            $table->string('defined_billing_city')->nullable();
            $table->string('defined_billing_country', 10)->nullable();
            $table->enum('billing_address_type', ['company_billing', 'department_shipping', 'department_defined'])->default('company_billing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
