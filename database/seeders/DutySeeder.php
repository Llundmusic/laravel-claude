<?php

namespace Database\Seeders;

use App\Models\Duty;
use Illuminate\Database\Seeder;

class DutySeeder extends Seeder
{
    public function run(): void
    {
        // null = "Check" (duty unknown), 0 = no duty
        $euRegions = [
            'Scotland & UK',
            'Faroes',
            'Iceland',
            'Norway Refa',
            'South Europe and North Africa',
            'Norway',
        ];

        $checkRegions = [
            'Canada',
            'Baltic and Caspian sea',
            'Australia - Far east',
            'Saudi - Oman',
            'Other',
        ];

        $euFactories = [
            'Plunge - Lithuania',
            'Siauliai - Lithuania',
            'Amposta - Spain',
            'Hildre - Norway',
        ];

        // EU/Norway/Lithuania/Spain factories: 0 for EU-accessible regions, null for rest
        foreach ($euFactories as $factory) {
            foreach ($euRegions as $region) {
                Duty::updateOrCreate(
                    ['factory' => $factory, 'sales_region' => $region],
                    ['duty_rate' => 0]
                );
            }
            foreach ($checkRegions as $region) {
                Duty::updateOrCreate(
                    ['factory' => $factory, 'sales_region' => $region],
                    ['duty_rate' => null]
                );
            }
        }

        // India factory: 0 for Norway/Scotland & UK/Norway Refa, null for everything else
        $indiaZeroRegions = ['Norway', 'Scotland & UK', 'Norway Refa'];
        $allRegions = array_merge($euRegions, $checkRegions);

        foreach ($allRegions as $region) {
            Duty::updateOrCreate(
                ['factory' => 'Aurangabad - India', 'sales_region' => $region],
                ['duty_rate' => in_array($region, $indiaZeroRegions) ? 0 : null]
            );
        }
    }
}
