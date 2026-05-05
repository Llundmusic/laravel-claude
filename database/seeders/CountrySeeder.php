<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // Clear country dropdown cache after seeding
        \Cache::forget('country_dropdown_data');
        $now = Carbon::now();
        $countries = [
            ['code' => 'NO', 'name_key' => 'country_no', 'phone_code' => '47'],
            ['code' => 'SE', 'name_key' => 'country_se', 'phone_code' => '46'],
            ['code' => 'DK', 'name_key' => 'country_dk', 'phone_code' => '45'],
            ['code' => 'FI', 'name_key' => 'country_fi', 'phone_code' => '358'],
            ['code' => 'IS', 'name_key' => 'country_is', 'phone_code' => '354'],
            ['code' => 'DE', 'name_key' => 'country_de', 'phone_code' => '49'],
            ['code' => 'FR', 'name_key' => 'country_fr', 'phone_code' => '33'],
            ['code' => 'ES', 'name_key' => 'country_es', 'phone_code' => '34'],
            ['code' => 'IT', 'name_key' => 'country_it', 'phone_code' => '39'],
            ['code' => 'NL', 'name_key' => 'country_nl', 'phone_code' => '31'],
            ['code' => 'BE', 'name_key' => 'country_be', 'phone_code' => '32'],
            ['code' => 'LU', 'name_key' => 'country_lu', 'phone_code' => '352'],
            ['code' => 'CH', 'name_key' => 'country_ch', 'phone_code' => '41'],
            ['code' => 'AT', 'name_key' => 'country_at', 'phone_code' => '43'],
            ['code' => 'IE', 'name_key' => 'country_ie', 'phone_code' => '353'],
            ['code' => 'GB', 'name_key' => 'country_gb', 'phone_code' => '44'],
            ['code' => 'PT', 'name_key' => 'country_pt', 'phone_code' => '351'],
            ['code' => 'PL', 'name_key' => 'country_pl', 'phone_code' => '48'],
            ['code' => 'CZ', 'name_key' => 'country_cz', 'phone_code' => '420'],
            ['code' => 'SK', 'name_key' => 'country_sk', 'phone_code' => '421'],
            ['code' => 'HU', 'name_key' => 'country_hu', 'phone_code' => '36'],
            ['code' => 'SI', 'name_key' => 'country_si', 'phone_code' => '386'],
            ['code' => 'HR', 'name_key' => 'country_hr', 'phone_code' => '385'],
            ['code' => 'RS', 'name_key' => 'country_rs', 'phone_code' => '381'],
            ['code' => 'ME', 'name_key' => 'country_me', 'phone_code' => '382'],
            ['code' => 'AL', 'name_key' => 'country_al', 'phone_code' => '355'],
            ['code' => 'MK', 'name_key' => 'country_mk', 'phone_code' => '389'],
            ['code' => 'BG', 'name_key' => 'country_bg', 'phone_code' => '359'],
            ['code' => 'RO', 'name_key' => 'country_ro', 'phone_code' => '40'],
            ['code' => 'GR', 'name_key' => 'country_gr', 'phone_code' => '30'],
            ['code' => 'LT', 'name_key' => 'country_lt', 'phone_code' => '370'],
            ['code' => 'LV', 'name_key' => 'country_lv', 'phone_code' => '371'],
            ['code' => 'EE', 'name_key' => 'country_ee', 'phone_code' => '372'],
            ['code' => 'UA', 'name_key' => 'country_ua', 'phone_code' => '380'],
            ['code' => 'BY', 'name_key' => 'country_by', 'phone_code' => '375'],
            ['code' => 'MD', 'name_key' => 'country_md', 'phone_code' => '373'],
            ['code' => 'RU', 'name_key' => 'country_ru', 'phone_code' => '7'],
            ['code' => 'SM', 'name_key' => 'country_sm', 'phone_code' => '378'],
            ['code' => 'MC', 'name_key' => 'country_mc', 'phone_code' => '377'],
            ['code' => 'LI', 'name_key' => 'country_li', 'phone_code' => '423'],
            ['code' => 'VA', 'name_key' => 'country_va', 'phone_code' => '39'],
            ['code' => 'AD', 'name_key' => 'country_ad', 'phone_code' => '376'],
            ['code' => 'MT', 'name_key' => 'country_mt', 'phone_code' => '356'],
            ['code' => 'CY', 'name_key' => 'country_cy', 'phone_code' => '357'],
        ];
        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['code' => $country['code']],
                [
                    'name_key' => $country['name_key'],
                    'phone_code' => $country['phone_code'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
