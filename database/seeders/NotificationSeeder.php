<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('role', UserRole::User)->first();
        if ($user === null) {
            return;
        }

        if ($user->notifications()->count() > 0) {
            return;
        }

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'seed',
            'data' => [
                'title' => 'Welcome',
                'body' => 'Your account is ready. Deposit funds to start bidding.',
            ],
            'read_at' => null,
        ]);
    }
}
