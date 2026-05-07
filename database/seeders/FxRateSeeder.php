<?php

namespace Database\Seeders;

use App\Models\FxRate;
use Illuminate\Database\Seeder;

class FxRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            'EUR' => 1.0,
            'USD' => 1.1694,
            'GBP' => 0.86575,
            'NOK' => 10.913,
            'DKK' => 7.473,
            'SEK' => 10.7795,
            'CHF' => 0.9177,
            'JPY' => 186.5,
            'CNY' => 7.9928,
            'AUD' => 1.6347,
            'CAD' => 1.5986,
        ];

        foreach ($rates as $currency => $rate) {
            FxRate::updateOrCreate(
                ['currency' => $currency],
                ['live_rate' => $rate, 'live_updated_at' => now()]
            );
        }
    }
}
