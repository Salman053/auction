<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'type' => fake()->randomElement(['deposit', 'withdrawal', 'auction_payment', 'refund']),
            'status' => 'pending',
            'amount_yen' => fake()->numberBetween(100, 500000),
            'provider' => 'stripe',
            'requested_by_user_id' => User::factory(),
            'memo' => fake()->sentence(),
        ];
    }
}
