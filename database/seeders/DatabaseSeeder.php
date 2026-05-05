<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in proper order (companies first, then roles)
        $this->call([
            CompanySeeder::class,
            DepartmentSeeder::class,
            RoleSeeder::class,
            CountrySeeder::class,
            // AccessGroupSeeder removed - rbac:sync handles access group discovery
            // UsersSeeder should be run separately after rbac:sync in deployment
        ]);
    }
}
