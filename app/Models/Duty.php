<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    protected $fillable = [
        'factory',
        'sales_region',
        'duty_rate',
    ];

    protected $casts = [
        'duty_rate' => 'float',
    ];
}
