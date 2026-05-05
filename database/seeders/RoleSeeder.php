<?php

namespace Database\Seeders;

use App\Models\AccessSystem\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'can_be_approver' => false,
                'is_active' => true,
                'is_system_role' => true,
            ],
            [
                'name' => 'System Administrator',
                'description' => 'Administrative access to manage users and companies',
                'can_be_approver' => false,
                'is_active' => true,
                'is_system_role' => true,
            ],
            [
                'name' => 'Default User',
                'description' => 'Standard user access with basic permissions',
                'can_be_approver' => false,
                'is_active' => true,
                'is_system_role' => true,
            ],
            [
                'name' => 'Company Administrator',
                'description' => 'Can manage company settings and users within their company, but has no access to system-wide settings. Can be set as purchaser and approver.',
                'can_be_approver' => true,
                'is_active' => true,
                'is_system_role' => false,
            ],
            [
                'name' => 'Purchaser',
                'description' => 'Can purchase items but has limited administrative access',
                'can_be_approver' => false,
                'is_active' => true,
                'is_system_role' => false,
            ],
            [
                'name' => 'Approver',
                'description' => 'Can approve requests but has limited administrative access',
                'can_be_approver' => true,
                'is_active' => true,
                'is_system_role' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
