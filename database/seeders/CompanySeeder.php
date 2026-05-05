<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Define companies to seed with random Norwegian addresses
        $companies = [
            [
                'name' => 'System',
                'slug' => 'system',
                'customer_number' => '0001',
                'email' => 'system.company@example.com',
                'phone_code' => 'NO',
                'phone' => '12345678',
                'billing_address_line1' => 'Admin Street 1',
                'billing_address_line2' => '',
                'billing_postal_code' => '0001',
                'billing_city' => 'Adminville',
                'billing_country' => 'NO',
            ],
            [
                'name' => 'Vónin Refa AS',
                'slug' => 'vonin-refa-as',
                'customer_number' => '0002',
                'email' => 'post@refa.no',
                'phone_code' => 'NO',
                'phone' => '77850500',
                'billing_address_line1' => 'Strandveien 70',
                'billing_address_line2' => '',
                'billing_postal_code' => '9300',
                'billing_city' => 'Finnsnes',
                'billing_country' => 'NO',
            ],
        ];

        $companiesToInsert = [];

        foreach ($companies as $companyData) {
            // Check if company already exists
            $existingCompany = DB::table('companies')
                ->where('email', $companyData['email'])
                ->first();

            if (! $existingCompany) {
                $companiesToInsert[] = [
                    'name' => $companyData['name'],
                    'slug' => $companyData['slug'],
                    'customer_number' => $companyData['customer_number'],
                    'email' => $companyData['email'],
                    'phone_code' => $companyData['phone_code'],
                    'phone' => $companyData['phone'],
                    'billing_address_line1' => $companyData['billing_address_line1'],
                    'billing_address_line2' => $companyData['billing_address_line2'],
                    'billing_postal_code' => $companyData['billing_postal_code'],
                    'billing_city' => $companyData['billing_city'],
                    'billing_country' => $companyData['billing_country'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert companies in batch
        if (! empty($companiesToInsert)) {
            DB::table('companies')->insert($companiesToInsert);
            $this->command->info('Inserted '.count($companiesToInsert).' new companies.');
        }

        $this->command->info('CompanySeeder completed successfully!');
        $this->command->info('Total companies processed: '.count($companies));
    }
}
