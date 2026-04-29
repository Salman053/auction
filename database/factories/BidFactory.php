<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bid>
 */
class BidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auction_id' => Auction::factory(),
            'user_id' => User::factory(),
            'amount_yen' => fake()->numberBetween(100, 100000),
            'max_amount_yen' => function (array $attributes) {
                return $attributes['amount_yen'] + fake()->numberBetween(0, 50000);
            },
            'status' => 'active',
            'placed_via' => 'manual',
        ];
    }
}
