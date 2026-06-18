<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs',    'abbreviation' => 'pcs'],
            ['name' => 'Set',    'abbreviation' => 'set'],
            ['name' => 'Kg',     'abbreviation' => 'kg'],
            ['name' => 'Batang', 'abbreviation' => 'btg'],
            ['name' => 'Meter',  'abbreviation' => 'm'],
            ['name' => 'Box',    'abbreviation' => 'box'],
            ['name' => 'Roll',   'abbreviation' => 'roll'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['name' => $unit['name']],
                ['abbreviation' => $unit['abbreviation']]
            );
        }
    }
}