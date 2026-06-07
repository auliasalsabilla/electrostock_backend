<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StorageLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Rak A1', 'code' => 'RAK-A1', 'is_active' => true],
            ['name' => 'Rak A2', 'code' => 'RAK-A2', 'is_active' => true],
            ['name' => 'Rak B1', 'code' => 'RAK-B1', 'is_active' => true],
        ];

        DB::table('storage_locations')->insertOrIgnore($locations);
    }
}