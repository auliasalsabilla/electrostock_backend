<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unit',  'abbreviation' => 'pcs'],
            ['name' => 'Box',   'abbreviation' => 'box'],
            ['name' => 'Lusin', 'abbreviation' => 'lsn'],
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Kg',    'abbreviation' => 'kg'],
        ];

        DB::table('units')->insertOrIgnore($units);
    }
}