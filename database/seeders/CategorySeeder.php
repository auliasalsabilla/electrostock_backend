<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Resistor',   'slug' => 'resistor',   'description' => 'Komponen resistor',   'is_active' => true],
            ['name' => 'Kapasitor',  'slug' => 'kapasitor',  'description' => 'Komponen kapasitor',  'is_active' => true],
            ['name' => 'LED',        'slug' => 'led',        'description' => 'Komponen LED',        'is_active' => true],
            ['name' => 'Transistor', 'slug' => 'transistor', 'description' => 'Komponen transistor', 'is_active' => true],
        ];

        DB::table('categories')->insertOrIgnore($categories);
    }
}