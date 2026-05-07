<?php

namespace App\Console\Commands;

use App\Services\AccessGroupSyncService;
use Illuminate\Console\Command;

class SyncAccessGroups extends Command
{
    protected $signature = 'access:sync-groups {--dry-run : Preview discovered groups without making changes}';

    protected $description = 'Auto-discover access groups from codebase and sync with database';

    public function handle(AccessGroupSyncService $service): void
    {
        if ($this->option('dry-run')) {
            $results = $service->dryRunSync();

            $this->info("Discovered: {$results['discovered']} groups");
            $this->newLine();

            $this->info('New groups (would be created):');
            foreach ($results['new_groups'] as $name) {
                $this->line("  + {$name}");
            }

            $this->newLine();
            $this->info('Existing groups:');
            foreach ($results['existing_groups'] as $name) {
                $this->line("  = {$name}");
            }

            $this->newLine();
            $this->info('Potentially unused (would be deactivated):');
            foreach ($results['potentially_unused'] as $name) {
                $this->line("  - {$name}");
            }

            return;
        }

        $results = $service->syncAccessGroups();

        $this->info('Sync complete.');
        $this->line("  Discovered:        {$results['discovered']}");
        $this->line("  New:               {$results['new']}");
        $this->line("  Updated:           {$results['updated']}");
        $this->line("  Deactivated:       {$results['deactivated']}");
        $this->line("  Admin assignments: {$results['admin_assignments']}");
    }
}
