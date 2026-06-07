<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT Elektronika Jaya',    'code' => 'SUP-001', 'is_active' => true],
            ['name' => 'CV Komponen Nusantara',  'code' => 'SUP-002', 'is_active' => true],
            ['name' => 'PT Tekno Mandiri',       'code' => 'SUP-003', 'is_active' => true],
        ];

        DB::table('suppliers')->insertOrIgnore($suppliers);
    }
}