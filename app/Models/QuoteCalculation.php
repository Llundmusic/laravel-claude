<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteCalculation extends Model
{
    protected $fillable = [
        'customer_name',
        'netlog_number',
        'net_type',
        'material',
        'number_of_units',
        'size',
        'sales_region',
        'factory',
        'master_currency',
        'inco_terms',
        'rate_mode',
        'status',
        'status_ready_at',
        'status_calculation_at',
        'status_proposed_at',
        'status_approved_at',
        'status_confirmed_at',
        'production_gp_percent',
        'sales_price',
        'sales_price_currency',
        'outbound_freight_cost',
        'outbound_freight_currency',
        'supervision_cost',
        'supervision_currency',
        'delivery_first_net_week',
        'delivery_last_net_week',
        'nets_per_truck',
        'transit_time',
        'est_week_of_delivery',
        'delivery_year',
        'deliver_to_country',
        'delivery_address',
        'total_net_weight',
        'weight_of_netting',
    ];

    protected $casts = [
        'number_of_units' => 'integer',
        'nets_per_truck' => 'integer',
        'delivery_year' => 'integer',
        'production_gp_percent' => 'float',
        'sales_price' => 'float',
        'outbound_freight_cost' => 'float',
        'supervision_cost' => 'float',
        'total_net_weight' => 'float',
        'weight_of_netting' => 'float',
        'status_ready_at' => 'datetime',
        'status_calculation_at' => 'datetime',
        'status_proposed_at' => 'datetime',
        'status_approved_at' => 'datetime',
        'status_confirmed_at' => 'datetime',
    ];

    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class)->orderBy('sort_order');
    }

    public function inboundFreightItems(): HasMany
    {
        return $this->hasMany(InboundFreightItem::class)->orderBy('sort_order');
    }

    public function productionCostItems(): HasMany
    {
        return $this->hasMany(ProductionCostItem::class)->orderBy('sort_order');
    }
}
