<?php

namespace App\Models;

use App\Models\AccessSystem\Company;
use App\Models\AccessSystem\Department;
use App\Models\AccessSystem\Role;
use App\Models\Common\UserFilter;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name', 'email', 'phone_code', 'phone', 'billing_reference', 'language', 'password', 'darkmode', 'is_active', 'active_company_id', 'access_all_companies'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, LogsActivity, Notifiable, TwoFactorAuthenticatable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone_code', 'phone', 'email', 'billing_reference', 'language', 'active_company_id', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $event) => "User {$event}");
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'darkmode' => 'boolean',
            'is_active' => 'boolean',
            'access_all_companies' => 'boolean',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }

    public function activeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'active_company_id');
    }

    public function getActiveCompanyRole(): ?Role
    {
        if (! $this->active_company_id) {
            return null;
        }

        $pivot = $this->companies()
            ->where('companies.id', $this->active_company_id)
            ->first()?->pivot;

        return $pivot && $pivot->role_id ? Role::find($pivot->role_id) : null;
    }

    public function purchaserDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_purchasers')->withTimestamps();
    }

    public function approverDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_approvers')->withTimestamps();
    }

    public function filters(): HasMany
    {
        return $this->hasMany(UserFilter::class);
    }
}
