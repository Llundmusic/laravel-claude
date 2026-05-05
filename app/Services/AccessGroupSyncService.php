<?php

namespace App\Services;

use App\Models\AccessSystem\AccessGroup;
use Illuminate\Support\Collection;

class AccessGroupSyncService
{
    /**
     * Ensure all given group names exist in the database.
     * Creates missing groups with is_active = false so they must be explicitly enabled.
     *
     * @param  array<string>  $groupNames
     */
    public function syncGroups(array $groupNames): void
    {
        foreach ($groupNames as $name) {
            AccessGroup::firstOrCreate(
                ['name' => $name],
                ['description' => null, 'is_active' => false]
            );
        }
    }

    /** @return Collection<AccessGroup> */
    public function getPotentiallyUnused(int $daysSince = 30): Collection
    {
        return AccessGroup::getPotentiallyUnused($daysSince);
    }
}
