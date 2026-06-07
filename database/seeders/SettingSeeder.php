<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['key' => 'app_name'],
            ['value' => 'ElectroStock']
        );

        Setting::firstOrCreate(
            ['key' => 'low_stock_threshold'],
            ['value' => '10']
        );

        Setting::firstOrCreate(
            ['key' => 'currency'],
            ['value' => 'IDR']
        );

        Setting::firstOrCreate(
            ['key' => 'date_format'],
            ['value' => 'd-m-Y']
        );
    }
}
