<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'System Administrator',
                'phone'    => '9999999999',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
                'status'   => true,
            ]
        );

        // Demo HR account
        User::updateOrCreate(
            ['email' => 'hr@easyloanpoint.com'],
            [
                'name'     => 'HR Manager',
                'phone'    => '9888888888',
                'email'    => 'hr@easyloanpoint.com',
                'password' => Hash::make('12345678'),
                'role'     => 'hr',
                'status'   => true,
            ]
        );

        $this->command->info('✓ Admin seeded: admin@gmail.com / 12345678');
        $this->command->info('✓ HR seeded:    hr@easyloanpoint.com / 12345678');
    }
}
