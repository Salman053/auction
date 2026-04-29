<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@example.com');
        $password = (string) env('ADMIN_PASSWORD', 'password');

        $admin = User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin', 'password' => $password],
        );

        $admin->forceFill([
            'role' => UserRole::Admin->value,
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ])->save();
    }
}
