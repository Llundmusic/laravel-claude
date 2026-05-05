<?php

namespace App\Models\AccessSystem;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Department extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id', 'name', 'email', 'phone_code', 'phone',
        'shipping_address_line1', 'shipping_address_line2', 'shipping_postal_code',
        'shipping_city', 'shipping_country',
        'defined_billing_address_line1', 'defined_billing_address_line2',
        'defined_billing_postal_code', 'defined_billing_city', 'defined_billing_country',
        'billing_address_type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'billing_address_type'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $event) => "Department {$event}");
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchasers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_purchasers')->withTimestamps();
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_approvers')->withTimestamps();
    }
}
