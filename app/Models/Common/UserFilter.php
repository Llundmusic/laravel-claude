<?php

namespace App\Models\Common;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFilter extends Model
{
    protected $fillable = ['user_id', 'page_name', 'filter_name', 'filter_values', 'filter_column', 'order'];

    protected $casts = [
        'filter_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
