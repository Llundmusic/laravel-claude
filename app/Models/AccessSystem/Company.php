<?php

namespace App\Models\AccessSystem;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Company extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'customer_number', 'email', 'phone_code', 'phone',
        'billing_address_line1', 'billing_address_line2', 'billing_postal_code',
        'billing_city', 'billing_country', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'customer_number', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $event) => "Company {$event}");
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function accessGroups(): BelongsToMany
    {
        return $this->belongsToMany(AccessGroup::class, 'access_group_companies');
    }

    public function hasAccessTo(string $groupName): bool
    {
        return $this->accessGroups()
            ->where('name', $groupName)
            ->where('is_active', true)
            ->exists();
    }
}
