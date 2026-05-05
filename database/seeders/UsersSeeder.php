<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $defaultPassword = Hash::make('asdfasdf');

        // Example country codes (expand as needed)
        $countryCodes = ['NO', 'LT', 'DK', 'FO', 'IS', 'SE', 'FI', 'GB', 'DE', 'FR'];

        // Helper to generate random phone number
        $randomPhone = function () {
            return str_pad(strval(rand(0, pow(10, 10) - 1)), rand(6, 10), '0', STR_PAD_LEFT);
        };

        // Helper to generate billing reference (initials + 4-5 digits)
        $randomBillingReference = function ($name) {
            $initials = collect(explode(' ', $name))->map(fn ($n) => strtoupper(substr($n, 0, 1)))->implode('');
            $digits = rand(1000, 99999);

            return $initials.$digits;
        };

        // Get the System Company ID (always id 1)
        $systemCompanyId = DB::table('companies')->where('id', 1)->value('id');

        if (! $systemCompanyId) {
            $this->command->error('Company with id 1 was not found. Please run company migrations first.');

            return;
        }

        // System admin user (will get Super Administrator role)
        $SuperAdminUser = [
            'name' => 'Super Administrator',
            'email' => 'superadmin@this.application',
            'password' => Hash::make('asdfasdf'),
            'language' => 'nb',
            'darkmode' => true,
            'is_active' => true,
            'access_all_companies' => true,
            'is_superadmin' => true, // Flag to identify admin user
        ];

        // Users specified by the client
        $specifiedUsers = [
            ['name' => 'Lasse Aspelund', 'email' => 'lasse.aspelund@refa.no'],
            ['name' => 'Lennart Nordhus', 'email' => 'lennart.nordhus@refa.no'],
            ['name' => 'Kim Simonsen', 'email' => 'kim.simonsen@refa.no'],
            ['name' => 'Lill-Tove Theodorsen', 'email' => 'lill-tove.theodorsen@refa.no'],
            ['name' => 'Justas Peseckis', 'email' => 'jp@vonin.com'],
            ['name' => 'Robin Klaussen', 'email' => 'robin.klaussen@refa.no'],
            ['name' => 'Frank Larsen', 'email' => 'frank.larsen@refa.no'],
            ['name' => 'Harriet Larsen', 'email' => 'harriet.larsen@refa.no'],
            ['name' => 'Greta Sviridova', 'email' => 'gs@vonin.com'],
            ['name' => 'Birgitte Falch Jakobsen', 'email' => 'birgitte.falch@refa.no'],
            ['name' => 'Notbehandling Rossvoll', 'email' => 'notbehandling.rossvoll@refa.no'],
            ['name' => 'Bøteri Finnsnes', 'email' => 'boteri.finnsnes@refa.no'],
            ['name' => 'Bøteri Rossvoll', 'email' => 'boteri.rossvoll@refa.no'],
            ['name' => 'Vaskeri Finnsnes', 'email' => 'vaskeri.finnsnes@refa.no'],
            ['name' => 'Bakgård Finnsnes', 'email' => 'bakgaard.finnsnes@refa.no'],
        ];

        // Additional generic users with example.com emails
        $genericUsers = [
            ['name' => 'John Anderson', 'email' => 'john.anderson@example.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@example.com'],
            ['name' => 'Michael Brown', 'email' => 'michael.brown@example.com'],
            ['name' => 'Emma Wilson', 'email' => 'emma.wilson@example.com'],
            ['name' => 'David Miller', 'email' => 'david.miller@example.com'],
            ['name' => 'Lisa Garcia', 'email' => 'lisa.garcia@example.com'],
            ['name' => 'Robert Taylor', 'email' => 'robert.taylor@example.com'],
            ['name' => 'Jennifer Martinez', 'email' => 'jennifer.martinez@example.com'],
            ['name' => 'William Davis', 'email' => 'william.davis@example.com'],
            ['name' => 'Amanda Rodriguez', 'email' => 'amanda.rodriguez@example.com'],
            ['name' => 'James Smith', 'email' => 'james.smith@example.com'],
            ['name' => 'Maria Lopez', 'email' => 'maria.lopez@example.com'],
            ['name' => 'Christopher Lee', 'email' => 'christopher.lee@example.com'],
            ['name' => 'Michelle White', 'email' => 'michelle.white@example.com'],
            ['name' => 'Daniel Thomas', 'email' => 'daniel.thomas@example.com'],
        ];

        // Combine all users (admin first, then default user, then others)
        $allUsers = array_merge([$SuperAdminUser], $specifiedUsers, $genericUsers);

        $usersToInsert = [];
        $companyUserRelations = [];

        foreach ($allUsers as $userData) {
            // Check if user already exists
            $existingUser = DB::table('users')->where('email', $userData['email'])->first();

            if (! $existingUser) {
                $phoneCode = $countryCodes[array_rand($countryCodes)];
                $phone = $randomPhone();
                $billingReference = $randomBillingReference($userData['name']);
                $usersToInsert[] = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone_code' => $phoneCode,
                    'phone' => $phone,
                    'billing_reference' => $billingReference,
                    'password' => $userData['password'] ?? $defaultPassword,
                    'language' => $userData['language'] ?? 'nb',
                    'darkmode' => $userData['darkmode'] ?? false,
                    'is_active' => $userData['is_active'] ?? true,
                    'access_all_companies' => $userData['access_all_companies'] ?? false,
                    'active_company_id' => null, // Will be set later based on role
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert users in batches
        if (! empty($usersToInsert)) {
            DB::table('users')->insert($usersToInsert);
            $this->command->info('Inserted '.count($usersToInsert).' new users.');
        }

        // Get role IDs
        $superAdminRoleId = DB::table('roles')->where('name', 'Super Administrator')->value('id');
        $defaultUserRoleId = DB::table('roles')->where('name', 'Purchaser')->value('id');

        if (! $superAdminRoleId || ! $defaultUserRoleId) {
            $this->command->error('Required roles not found. Please run RBAC seeders first.');

            return;
        }

        // Get company IDs
        $companyIds = [
            'system' => DB::table('companies')->where('id', 1)->value('id'),
            'vr' => DB::table('companies')->where('name', 'Vónin Refa AS')->value('id'),
        ];

        // Now get all user IDs and create company_user relationships
        foreach ($allUsers as $userData) {
            $userId = DB::table('users')->where('email', $userData['email'])->value('id');

            if ($userId) {
                // Determine role and companies based on user type
                $isSuperAdmin = isset($userData['is_superadmin']) && $userData['is_superadmin'] === true;

                if ($isSuperAdmin) {
                    // Admin user gets Super Administrator role and access to all companies
                    $roleId = $superAdminRoleId;
                    $companies = array_filter($companyIds); // Remove null values
                    // Always set admin's active company to company with id 1 during seeding since it has all access groups
                    $activeCompanyId = $companyIds['system'];
                } else {
                    // All other users get Default User role and access to company with id 1 only
                    $roleId = $defaultUserRoleId;
                    $companies = [$companyIds['system']];
                    $activeCompanyId = $companyIds['system'];
                }

                // Set active company for the user
                DB::table('users')->where('id', $userId)->update(['active_company_id' => $activeCompanyId]);

                // Create company relationships
                foreach ($companies as $companyId) {
                    if ($companyId) {
                        // Check if relationship already exists
                        $existingRelation = DB::table('company_user')
                            ->where('company_id', $companyId)
                            ->where('user_id', $userId)
                            ->exists();

                        if (! $existingRelation) {
                            $companyUserRelations[] = [
                                'company_id' => $companyId,
                                'user_id' => $userId,
                                'role_id' => $roleId,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }
            }
        }

        // Insert company_user relationships
        if (! empty($companyUserRelations)) {
            DB::table('company_user')->insert($companyUserRelations);
            $this->command->info('Created '.count($companyUserRelations).' company-user relationships.');
        }

        $this->command->info('UsersSeeder completed successfully!');
        $this->command->info('Total users processed: '.count($allUsers));
        $this->command->info('Admin user assigned Super Administrator role to all companies.');
        $this->command->info('All other users ('.(count($allUsers) - 1).') assigned Default User role to company with id 1.');
    }
}
