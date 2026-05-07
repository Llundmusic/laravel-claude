<?php

namespace Database\Seeders;

use App\Models\GpBudget;
use Illuminate\Database\Seeder;

class GpBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $budgets = [
            'Scotland & UK' => 0.2252,
            'Faroes' => 0.2306,
            'Iceland' => 0.22,
            'Norway Refa' => 0.2146,
            'Canada' => 0.2603,
            'South Europe and North Africa' => 0.25,
            'Norway' => 0.1593,
            'Baltic and Caspian sea' => 0.25,
            'Australia - Far east' => 0.25,
            'Saudi - Oman' => 0.25,
            'Other' => 0.25,
        ];

        foreach ($budgets as $region => $target) {
            GpBudget::updateOrCreate(
                ['sales_region' => $region],
                ['gp_target' => $target]
            );
        }
    }
}
