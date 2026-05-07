<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FxRate extends Model
{
    protected $fillable = [
        'currency',
        'live_rate',
        'frozen_rate',
        'live_updated_at',
        'frozen_at',
    ];

    protected $casts = [
        'live_rate' => 'float',
        'frozen_rate' => 'float',
        'live_updated_at' => 'datetime',
        'frozen_at' => 'datetime',
    ];
}
