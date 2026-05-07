<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    protected $fillable = [
        'quote_calculation_id',
        'sort_order',
        'description',
        'qty',
        'unit',
        'unit_cost',
        'currency',
    ];

    protected $casts = [
        'qty' => 'float',
        'unit_cost' => 'float',
        'sort_order' => 'integer',
    ];

    public function quoteCalculation(): BelongsTo
    {
        return $this->belongsTo(QuoteCalculation::class);
    }
}
