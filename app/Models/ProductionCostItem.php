<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionCostItem extends Model
{
    protected $fillable = [
        'quote_calculation_id',
        'sort_order',
        'description',
        'direct_hours',
        'direct_rate',
        'currency',
        'benchmark_hours',
    ];

    protected $casts = [
        'direct_hours' => 'float',
        'direct_rate' => 'float',
        'benchmark_hours' => 'float',
        'sort_order' => 'integer',
    ];

    public function quoteCalculation(): BelongsTo
    {
        return $this->belongsTo(QuoteCalculation::class);
    }
}
