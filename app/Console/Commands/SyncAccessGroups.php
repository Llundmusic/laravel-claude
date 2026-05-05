<?php

namespace App\Console\Commands;

use App\Services\AccessGroupSyncService;
use Illuminate\Console\Command;

class SyncAccessGroups extends Command
{
    protected $signature = 'access:sync-groups {--groups=* : Group names to ensure exist}';

    protected $description = 'Ensure all defined access groups exist in the database';

    public function handle(AccessGroupSyncService $service): void
    {
        $groups = $this->option('groups');

        if (empty($groups)) {
            $this->warn('No group names provided. Pass --groups=basic_access --groups=administration_users etc.');

            return;
        }

        $service->syncGroups($groups);
        $this->info('Access groups synced: '.implode(', ', $groups));
    }
}
