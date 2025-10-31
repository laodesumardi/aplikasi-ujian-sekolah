<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Guru contoh
        User::query()->firstOrCreate(
            ['email' => 'ani.guru@example.com'],
            [
                'name' => 'Bu Ani',
                'password' => 'password',
                'role' => 'guru',
                'kelas' => null,
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'dedi.guru@example.com'],
            [
                'name' => 'Pak Dedi',
                'password' => 'password',
                'role' => 'guru',
                'kelas' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}