<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixAdminCompanyAssignments extends Command
{
    protected $signature = 'admin:fix-company-assignments
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Ensure all users have a valid active_company_id that matches one of their assigned companies';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');
        $fixed = 0;

        User::with('companies')->each(function (User $user) use ($dryRun, &$fixed) {
            $companyIds = $user->companies->pluck('id');

            if ($companyIds->isEmpty()) {
                return;
            }

            if (! $user->active_company_id || ! $companyIds->contains($user->active_company_id)) {
                $newCompanyId = $companyIds->first();
                $this->line("User {$user->id} ({$user->email}): active_company_id {$user->active_company_id} → {$newCompanyId}");

                if (! $dryRun) {
                    $user->update(['active_company_id' => $newCompanyId]);
                }

                $fixed++;
            }
        });

        $this->info($dryRun ? "Dry run: {$fixed} users would be fixed." : "{$fixed} users fixed.");
    }
}
