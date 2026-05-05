<?php

namespace App\Services;

use App\Models\AccessSystem\AccessGroup;
use App\Models\AccessSystem\Company;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AccessLevelService
{
    /**
     * Check if a user (or the authenticated user) has access to an access group.
     *
     * Accepts either (User $user, string $groupName) or (string $groupName).
     */
    public function hasAccess(mixed $userOrGroupName, ?string $groupName = null): bool
    {
        if ($groupName === null) {
            $user = Auth::user();
            $groupName = $userOrGroupName;
        } else {
            $user = $userOrGroupName;
        }

        if (! $user || ! $user->active_company_id) {
            return false;
        }

        return $this->hasGroupAccess($user, $groupName);
    }

    public function hasGroupAccess(?User $user, string $groupName): bool
    {
        if (! $user || ! $user->active_company_id) {
            return false;
        }

        $role = $user->getActiveCompanyRole();
        $roleId = $role ? $role->id : 'none';
        $cacheKey = "user_access_{$user->id}_{$user->active_company_id}_{$roleId}_{$groupName}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user, $groupName) {
            return $this->checkRbacAccess($user, $groupName);
        });
    }

    private function checkRbacAccess(User $user, string $groupName): bool
    {
        $role = $user->getActiveCompanyRole();
        $company = $user->activeCompany;

        if (! $role || ! $company) {
            return false;
        }

        $accessGroup = AccessGroup::where('name', $groupName)->where('is_active', true)->first();

        if (! $accessGroup) {
            return false;
        }

        $hasRoleRestrictions = $accessGroup->roles()->exists();
        $hasCompanyRestrictions = $accessGroup->companies()->exists();

        // Groups with no assignments are denied by default
        if (! $hasRoleRestrictions && ! $hasCompanyRestrictions) {
            return false;
        }

        $roleHasAccess = ! $hasRoleRestrictions || $role->hasAccessTo($groupName);
        $companyHasAccess = ! $hasCompanyRestrictions || $company->hasAccessTo($groupName);

        return $roleHasAccess && $companyHasAccess;
    }

    public function clearRoleAssignmentCache(AccessGroup $accessGroup, array $roleIds): void
    {
        foreach ($accessGroup->companies as $company) {
            foreach ($company->users as $user) {
                $pivot = $user->companies()->where('companies.id', $company->id)->first()?->pivot;
                $roleId = $pivot?->role_id;
                if ($roleId && in_array($roleId, $roleIds)) {
                    Cache::forget("user_access_{$user->id}_{$company->id}_{$roleId}_{$accessGroup->name}");
                }
            }
        }
    }

    public function clearCompanyAssignmentCache(AccessGroup $accessGroup, array $companyIds): void
    {
        $roleIds = $accessGroup->roles()->pluck('roles.id')->toArray();

        foreach ($companyIds as $companyId) {
            $company = Company::find($companyId);
            if (! $company) {
                continue;
            }

            foreach ($company->users as $user) {
                $pivot = $user->companies()->where('companies.id', $company->id)->first()?->pivot;
                $roleId = $pivot?->role_id;
                if ($roleId && in_array($roleId, $roleIds)) {
                    Cache::forget("user_access_{$user->id}_{$company->id}_{$roleId}_{$accessGroup->name}");
                }
            }
        }
    }

    public function getUserAccessGroups(User $user): Collection
    {
        if (! $user->active_company_id) {
            return collect();
        }

        $role = $user->getActiveCompanyRole();
        $company = $user->activeCompany;

        if (! $role || ! $company) {
            return collect();
        }

        return AccessGroup::where('is_active', true)->get()->filter(
            fn ($group) => $this->checkRbacAccess($user, $group->name)
        );
    }
}
