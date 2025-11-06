<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Only create if not exists
        User::firstOrCreate(
            ['email' => 'superadmin@siatex.com'],
            [
                'name' => 'Siatex Super Admin',
                'password' => Hash::make('password'), // change in production
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}