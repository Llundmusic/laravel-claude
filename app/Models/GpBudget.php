<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpBudget extends Model
{
    protected $fillable = [
        'sales_region',
        'gp_target',
    ];

    protected $casts = [
        'gp_target' => 'float',
    ];
}
