<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User default
        User::firstOrCreate(
            ['email' => 'admin@electrostock.com'],
            [
                'name'      => 'Administrator',
                'password'  => 'admin123',
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@electrostock.com'],
            [
                'name'      => 'Staff',
                'password'  => 'staff123',
                'role'      => 'staff',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@electrostock.com'],
            [
                'name'      => 'Manager',
                'password'  => 'manager123',
                'role'      => 'manager',
                'is_active' => true,
            ]
        );

        // Data master
        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            UnitSeeder::class,
            StorageLocationSeeder::class,
            SettingSeeder::class,
        ]);
    }
}