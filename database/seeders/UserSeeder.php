<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

    }
}
