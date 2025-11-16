<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class UsersImport implements ToCollection, WithHeadingRow
{
    protected int $created = 0;
    protected int $updated = 0;
    protected int $skipped = 0;
    protected array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $name = trim((string)($row['name'] ?? ''));
            $email = strtolower(trim((string)($row['email'] ?? '')));
            $role = strtolower(trim((string)($row['role'] ?? '')));
            $kelas = trim((string)($row['kelas'] ?? ''));
            $password = (string)($row['password'] ?? '');

            if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['admin','guru','siswa'])) {
                $this->skipped++;
                $this->errors[] = "Baris " . ($index + 2) . " tidak valid (wajib: name,email,role)."; // +2 karena heading row
                continue;
            }

            $existing = User::where('email', $email)->first();

            if ($existing) {
                $data = [
                    'name' => $name,
                    'role' => $role,
                    'kelas' => $kelas ?: null,
                ];
                if (!empty($password)) {
                    $data['password'] = Hash::make($password);
                }
                $existing->update($data);
                $this->updated++;
            } else {
                $pwd = !empty($password) ? $password : Str::random(10);
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($pwd),
                    'role' => $role,
                    'kelas' => $kelas ?: null,
                ]);
                $this->created++;
            }
        }
    }

    public function report(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }
}