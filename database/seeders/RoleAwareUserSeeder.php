<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAwareUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@libratech.test'],
            [
                'name' => 'Admin LibraTech',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        $members = [
            ['name' => 'Budi Santoso', 'email' => 'budi@libratech.test'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@libratech.test'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@libratech.test'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@libratech.test'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi@libratech.test'],
        ];

        foreach ($members as $m) {
            User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Member,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
