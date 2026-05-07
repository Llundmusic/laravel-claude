<?php

namespace App\Services;

use App\Models\AccessSystem\AccessGroup;
use App\Models\AccessSystem\Company;
use App\Models\AccessSystem\Role;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Finder\Finder;

class AccessGroupSyncService
{
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function syncAccessGroups(): array
    {
        Log::info('Starting access group sync...');

        $discoveredGroups = $this->discoverGroupsFromCodebase();
        $syncResults = $this->syncGroupsWithDatabase($discoveredGroups);

        Log::info('Access group sync completed', $syncResults);

        return $syncResults;
    }

    public function discoverGroupsFromCodebase(): Collection
    {
        $groups = collect();

        $paths = [
            app_path('Http/Controllers'),
            resource_path('views'),
            app_path('Services'),
            app_path('Http/Middleware'),
            app_path('Traits'),
            app_path('Providers'),
            app_path('Livewire'),
        ];

        foreach ($paths as $path) {
            $groups = $groups->merge($this->scanDirectory($path));
        }

        return $groups->unique()->filter()->values();
    }

    private function scanDirectory(string $path): Collection
    {
        if (! is_dir($path)) {
            return collect();
        }

        $finder = new Finder;
        $files = $finder->files()
            ->in($path)
            ->name(['*.php', '*.blade.php'])
            ->getIterator();

        $groups = collect();

        foreach ($files as $file) {
            $groups = $groups->merge($this->extractGroupsFromFile($file->getPathname()));
        }

        return $groups;
    }

    private function extractGroupsFromFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if (! $content) {
            return [];
        }

        $groups = [];

        $filterGroupName = fn ($arr) => array_filter($arr, fn ($g) => $g !== 'group_name');

        // ->hasAccess(user, 'group_name')
        preg_match_all('/->hasAccess\([^,]+,\s*[\'\"]([^\'\"]+)[\'\"]\s*\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // hasAccess('group_name')
        preg_match_all('/(?<!\w)hasAccess\([\'\"]([^\'\"]+)[\'\"]\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // ->hasGroupAccess(user, 'group_name')
        preg_match_all('/->hasGroupAccess\([^,]+,\s*[\'\"]([^\'\"]+)[\'\"]\s*\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // hasGroupAccess('group_name')
        preg_match_all('/(?<!\w)hasGroupAccess\([\'\"]([^\'\"]+)[\'\"]\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // ->hasAccessTo('group_name')
        preg_match_all('/->hasAccessTo\([\'\"]([^\'\"]+)[\'\"]\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // hasAccessTo('group_name')
        preg_match_all('/(?<!\w)hasAccessTo\([\'\"]([^\'\"]+)[\'\"]\)/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // $canAccess_GroupName variables
        preg_match_all('/\$canAccess_([a-zA-Z_][a-zA-Z0-9_]*)/', $content, $matches);
        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $snake = $this->convertToSnakeCase($match);
                if ($snake !== 'group_name') {
                    $groups[] = $snake;
                }
            }
        }

        // 'access:group_name' middleware
        preg_match_all('/[\'\"]access:([a-zA-Z_][a-zA-Z0-9_]*)[\'\"]/', $content, $matches);
        if (! empty($matches[1])) {
            $groups = array_merge($groups, $filterGroupName($matches[1]));
        }

        // $accessChecks['canAccess_GroupName']
        preg_match_all('/\$accessChecks\[[\'\"]canAccess_([a-zA-Z_][a-zA-Z0-9_]*)[\'\"]\]/', $content, $matches);
        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $snake = $this->convertToSnakeCase($match);
                if ($snake !== 'group_name') {
                    $groups[] = $snake;
                }
            }
        }

        return array_unique($groups);
    }

    private function convertToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    private function syncGroupsWithDatabase(Collection $discoveredGroups): array
    {
        $results = [
            'discovered' => $discoveredGroups->count(),
            'new' => 0,
            'updated' => 0,
            'deactivated' => 0,
            'admin_assignments' => 0,
        ];

        foreach ($discoveredGroups as $groupName) {
            $group = AccessGroup::firstOrNew(['name' => $groupName]);
            $isNewGroup = ! $group->exists;

            if ($isNewGroup) {
                $group->fill([
                    'description' => $this->generateDefaultDescription($groupName),
                    'is_active' => true,
                    'last_seen_at' => now(),
                ]);
                $group->save();
                $results['new']++;

                $this->assignDefaultAdminAccess($group);
                $results['admin_assignments']++;
            } else {
                $group->markAsSeen();
                $this->assignCompanyAccess($group);
                $results['updated']++;
            }
        }

        $unusedGroups = AccessGroup::getPotentiallyUnused(7);
        foreach ($unusedGroups as $group) {
            if ($group->is_active) {
                $group->update(['is_active' => false]);
                $results['deactivated']++;
            }
        }

        return $results;
    }

    private function generateDefaultDescription(string $groupName): string
    {
        $titleCase = implode(' ', array_map('ucfirst', explode('_', $groupName)));

        return "Access to {$titleCase} functionality";
    }

    private function assignDefaultAdminAccess(AccessGroup $group): void
    {
        try {
            $roles = [
                'super_admin' => Role::where('name', 'Super Administrator')->first(),
                'admin' => Role::where('name', 'System Administrator')->first(),
                'user' => Role::where('name', 'Default User')->first(),
            ];

            if (in_array($group->name, ['administration_system', 'administration_access-groups'])) {
                $roleAssignments = [$roles['super_admin']->id ?? null];
            } elseif (str_contains($group->name, 'user_') ||
                str_contains($group->name, 'company_') ||
                str_contains($group->name, 'role_')) {
                $roleAssignments = array_filter([
                    $roles['super_admin']->id ?? null,
                    $roles['admin']->id ?? null,
                ]);
            } elseif ($group->name === 'basic_access') {
                $roleAssignments = array_filter([
                    $roles['super_admin']->id ?? null,
                    $roles['admin']->id ?? null,
                    $roles['user']->id ?? null,
                ]);
            } else {
                $roleAssignments = [$roles['super_admin']->id ?? null];
            }

            $roleAssignments = array_filter($roleAssignments);
            if (! empty($roleAssignments)) {
                $group->roles()->syncWithoutDetaching($roleAssignments);
                Log::info("Assigned roles to group {$group->name}: ".implode(', ', $roleAssignments));
            }

            $this->assignCompanyAccess($group);
        } catch (\Exception $e) {
            Log::error("Failed to assign roles to group {$group->name}: ".$e->getMessage());
        }
    }

    private function assignCompanyAccess(AccessGroup $group): void
    {
        try {
            foreach (Company::all() as $company) {
                if (! $group->companies()->where('company_id', $company->id)->exists()) {
                    $group->companies()->attach($company->id);
                    Log::info("Assigned company {$company->name} to access group: {$group->name}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to assign company access to group {$group->name}: ".$e->getMessage());
        }
    }

    public function dryRunSync(): array
    {
        $discoveredGroups = $this->discoverGroupsFromCodebase();
        $existingGroups = AccessGroup::pluck('name')->toArray();

        return [
            'discovered' => $discoveredGroups->count(),
            'new_groups' => $discoveredGroups->diff($existingGroups)->values()->toArray(),
            'existing_groups' => $discoveredGroups->intersect($existingGroups)->values()->toArray(),
            'potentially_unused' => AccessGroup::getPotentiallyUnused(7)->pluck('name')->toArray(),
        ];
    }
}
