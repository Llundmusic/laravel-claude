<?php

namespace Database\Seeders;

use App\Models\AccessSystem\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 random departments for company with id 1
        Department::factory()->count(3)->create([
            'company_id' => 1,
        ]);

        // Manually define 1 department for company with id 2
        Department::create([
            'company_id' => 2,
            'name' => 'Finnsnes Butikk',
            'email' => 'post@refa.no',
            'phone_code' => 'NO',
            'phone' => '77850500',
            'shipping_address_line1' => 'Strandveien 72',
            'shipping_address_line2' => '',
            'shipping_postal_code' => '9300',
            'shipping_city' => 'Finnsnes',
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => null,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => null,
            'defined_billing_city' => null,
            'defined_billing_country' => null,
            'billing_address_type' => 'company_billing',
        ]);
        Department::create([
            'company_id' => 2,
            'name' => 'Finnsnes Bøteri',
            'email' => 'post@refa.no',
            'phone_code' => 'NO',
            'phone' => '77850500',
            'shipping_address_line1' => 'Strandveien 74',
            'shipping_address_line2' => '',
            'shipping_postal_code' => '9300',
            'shipping_city' => 'Finnsnes',
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => null,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => null,
            'defined_billing_city' => null,
            'defined_billing_country' => null,
            'billing_address_type' => 'company_billing',
        ]);
        Department::create([
            'company_id' => 2,
            'name' => 'Tromsø Butikk',
            'email' => 'post@refa.no',
            'phone_code' => 'NO',
            'phone' => '77850500',
            'shipping_address_line1' => 'Stakkevollveien 67',
            'shipping_address_line2' => '',
            'shipping_postal_code' => '9010',
            'shipping_city' => 'Tromsø',
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => null,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => null,
            'defined_billing_city' => null,
            'defined_billing_country' => null,
            'billing_address_type' => 'company_billing',
        ]);
        Department::create([
            'company_id' => 2,
            'name' => 'Svolvær Butikk',
            'email' => 'post@refa.no',
            'phone_code' => 'NO',
            'phone' => '77850500',
            'shipping_address_line1' => 'Sjømannsgata 4',
            'shipping_address_line2' => '',
            'shipping_postal_code' => '8300',
            'shipping_city' => 'Svolvær',
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => null,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => null,
            'defined_billing_city' => null,
            'defined_billing_country' => null,
            'billing_address_type' => 'company_billing',
        ]);
        Department::create([
            'company_id' => 2,
            'name' => 'Lager Rossvoll',
            'email' => 'post@refa.no',
            'phone_code' => 'NO',
            'phone' => '77850500',
            'shipping_address_line1' => 'Øvre Rossvoll 71',
            'shipping_address_line2' => '',
            'shipping_postal_code' => '9322',
            'shipping_city' => 'Karlstad',
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => null,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => null,
            'defined_billing_city' => null,
            'defined_billing_country' => null,
            'billing_address_type' => 'company_billing',
        ]);
    }
}
