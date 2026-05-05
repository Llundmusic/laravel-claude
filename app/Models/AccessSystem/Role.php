<?php

namespace App\Models\AccessSystem;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Role extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'can_be_approver', 'is_active', 'is_system_role'];

    protected $casts = [
        'can_be_approver' => 'boolean',
        'is_active' => 'boolean',
        'is_system_role' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'can_be_approver', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $event) => "Role {$event}");
    }

    public function accessGroups(): BelongsToMany
    {
        return $this->belongsToMany(AccessGroup::class, 'access_group_roles');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot(['company_id'])
            ->withTimestamps();
    }

    public function hasAccessTo(string $groupName): bool
    {
        return $this->accessGroups()
            ->where('name', $groupName)
            ->where('is_active', true)
            ->exists();
    }
}
