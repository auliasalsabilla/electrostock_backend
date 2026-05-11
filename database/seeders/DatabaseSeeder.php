<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@electrostock.com',
            'password'  => 'password',   // otomatis di-hash oleh $casts
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }
}