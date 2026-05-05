<?php

namespace Database\Factories\AccessSystem;

use App\Models\AccessSystem\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'company_id' => 1, // Override in seeder if needed
            'name' => $this->faker->company . ' Department',
            'email' => $this->faker->unique()->companyEmail,
            'phone_code' => 'NO',
            'phone' => $this->faker->numerify('########'),
            'shipping_address_line1' => $this->faker->streetAddress,
            'shipping_address_line2' => '',
            'shipping_postal_code' => $this->faker->postcode,
            'shipping_city' => $this->faker->city,
            'shipping_country' => 'NO',
            'defined_billing_address_line1' => $this->faker->streetAddress,
            'defined_billing_address_line2' => '',
            'defined_billing_postal_code' => $this->faker->postcode,
            'defined_billing_city' => $this->faker->city,
            'defined_billing_country' => 'NO',
            'billing_address_type' => $this->faker->randomElement(['company_billing', 'department_shipping', 'department_defined']),
        ];
    }
}
