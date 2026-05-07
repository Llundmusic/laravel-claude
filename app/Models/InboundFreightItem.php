<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundFreightItem extends Model
{
    protected $fillable = [
        'quote_calculation_id',
        'sort_order',
        'description',
        'cost',
        'currency',
    ];

    protected $casts = [
        'cost' => 'float',
        'sort_order' => 'integer',
    ];

    public function quoteCalculation(): BelongsTo
    {
        return $this->belongsTo(QuoteCalculation::class);
    }
}
