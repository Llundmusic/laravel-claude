<?php

namespace App\Models\AccessSystem;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AccessGroup extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'is_active', 'last_seen_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $event) => "AccessGroup {$event}");
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'access_group_roles');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'access_group_companies');
    }

    public function markAsSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    public static function getPotentiallyUnused(int $daysSince = 30): Collection
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($daysSince) {
                $q->whereNull('last_seen_at')
                    ->orWhere('last_seen_at', '<', now()->subDays($daysSince));
            })
            ->get();
    }
}
