<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) && blank($password)) {
            return;
        }

        if (blank($email) || blank($password)) {
            throw new RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD harus diisi bersama-sama.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name'),
                'password' => $password,
                'is_admin' => true,
            ],
        );
    }
}
