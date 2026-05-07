<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('netlog_number')->nullable();
            $table->string('net_type')->nullable();
            $table->string('material')->nullable();
            $table->integer('number_of_units')->nullable();
            $table->string('size')->nullable();
            $table->string('sales_region')->nullable();
            $table->string('factory')->nullable();
            $table->string('master_currency')->default('EUR');
            $table->string('inco_terms')->nullable();
            $table->enum('rate_mode', ['live', 'frozen'])->default('live');
            $table->enum('status', ['draft', 'ready', 'calculation_complete', 'proposed_complete', 'market_approved', 'customer_confirmed'])->default('draft');
            $table->timestamp('status_ready_at')->nullable();
            $table->timestamp('status_calculation_at')->nullable();
            $table->timestamp('status_proposed_at')->nullable();
            $table->timestamp('status_approved_at')->nullable();
            $table->timestamp('status_confirmed_at')->nullable();
            $table->decimal('production_gp_percent', 5, 4)->nullable();
            $table->decimal('sales_price', 15, 2)->nullable();
            $table->string('sales_price_currency')->nullable();
            $table->decimal('outbound_freight_cost', 15, 2)->nullable();
            $table->string('outbound_freight_currency')->nullable();
            $table->decimal('supervision_cost', 15, 2)->nullable();
            $table->string('supervision_currency')->nullable();
            $table->string('delivery_first_net_week')->nullable();
            $table->string('delivery_last_net_week')->nullable();
            $table->integer('nets_per_truck')->nullable();
            $table->string('transit_time')->nullable();
            $table->string('est_week_of_delivery')->nullable();
            $table->smallInteger('delivery_year')->nullable();
            $table->string('deliver_to_country')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('total_net_weight', 10, 2)->nullable();
            $table->decimal('weight_of_netting', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_calculations');
    }
};
