<?php

namespace App\Console\Commands;

use App\Models\AccessSystem\Role;
use Illuminate\Console\Command;

class ManageRoles extends Command
{
    protected $signature = 'roles:manage
                            {action : create|list|deactivate}
                            {--name= : Role name}
                            {--description= : Role description}
                            {--approver : Mark role as can_be_approver}
                            {--system : Mark role as is_system_role}';

    protected $description = 'Manage application roles from the command line';

    public function handle(): void
    {
        match ($this->argument('action')) {
            'list' => $this->listRoles(),
            'create' => $this->createRole(),
            'deactivate' => $this->deactivateRole(),
            default => $this->error('Unknown action. Use create, list, or deactivate.'),
        };
    }

    private function listRoles(): void
    {
        $roles = Role::orderBy('name')->get(['id', 'name', 'description', 'is_active', 'is_system_role']);
        $this->table(['ID', 'Name', 'Description', 'Active', 'System'], $roles->toArray());
    }

    private function createRole(): void
    {
        $name = $this->option('name') ?? $this->ask('Role name');

        if (Role::where('name', $name)->exists()) {
            $this->warn("Role '{$name}' already exists.");

            return;
        }

        Role::create([
            'name' => $name,
            'description' => $this->option('description'),
            'can_be_approver' => (bool) $this->option('approver'),
            'is_system_role' => (bool) $this->option('system'),
            'is_active' => true,
        ]);

        $this->info("Role '{$name}' created.");
    }

    private function deactivateRole(): void
    {
        $name = $this->option('name') ?? $this->ask('Role name');
        $role = Role::where('name', $name)->first();

        if (! $role) {
            $this->error("Role '{$name}' not found.");

            return;
        }

        $role->update(['is_active' => false]);
        $this->info("Role '{$name}' deactivated.");
    }
}
