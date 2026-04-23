<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Utama',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff Gudang',
                'role' => 'staff',
                'password' => 'staff123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager',
                'role' => 'manager',
                'password' => 'manager123',
            ]
        );
    }
}
