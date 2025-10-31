<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Siswa demo (sesuai kebutuhan awal)
        User::query()->firstOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Siswa Demo',
                'password' => 'password',
                'role' => 'siswa',
                'kelas' => 'X IPA 1',
                'email_verified_at' => now(),
            ]
        );

        // Tambahan beberapa siswa
        User::query()->firstOrCreate(
            ['email' => 'budi.siswa@example.com'],
            [
                'name' => 'Budi',
                'password' => 'password',
                'role' => 'siswa',
                'kelas' => 'X IPA 1',
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'citra.siswa@example.com'],
            [
                'name' => 'Citra',
                'password' => 'password',
                'role' => 'siswa',
                'kelas' => 'X IPA 2',
                'email_verified_at' => now(),
            ]
        );
    }
}