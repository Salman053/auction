<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class BidderUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = (string) env('BIDDER_PASSWORD', 'password');
        $seedBalanceYen = (int) env('BIDDER_SEED_BALANCE_YEN', 100_000);

        $bidders = [
            ['name' => 'Test User', 'email' => 'test@example.com'],
            ['name' => 'Test Gmail', 'email' => 'test@gmail.com'],
            ['name' => 'Bidder One', 'email' => 'bidder1@example.com'],
            ['name' => 'Bidder Two', 'email' => 'bidder2@example.com'],
        ];

        foreach ($bidders as $bidder) {
            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $bidder['email']],
                [
                    'name' => $bidder['name'],
                    'password' => $password,
                    'role' => UserRole::User->value,
                ]
            );

            $user->wallet()->updateOrCreate([], [
                'balance_yen' => $seedBalanceYen,
                'locked_balance_yen' => 0,
                'withdrawal_locked_yen' => 0,
            ]);
        }
    }
}
