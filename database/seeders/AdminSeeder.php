<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_DEFAULT_EMAIL', 'admin@oclock.test');
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', 'admin123');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin O\'Clock',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );
    }
}
