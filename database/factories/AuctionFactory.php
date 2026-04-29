<?php

namespace Database\Factories;

use App\Models\Auction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startingBid = fake()->numberBetween(10_000, 200_000);
        $currentBid = $startingBid + fake()->numberBetween(0, 300_000);
        $endsAt = now()->addMinutes(fake()->numberBetween(10, 60 * 48));

        return [
            'yahoo_auction_id' => (string) fake()->unique()->numerify('##########'),
            'title' => fake()->randomElement([
                'Seiko Prospex Diver 200m',
                'Rolex Oyster Perpetual (Used)',
                'Omega Speedmaster Professional',
                'Grand Seiko Spring Drive',
                'Casio G-Shock Limited Edition',
                'Tag Heuer Carrera Chronograph',
                'Tudor Black Bay 58',
            ]).' - '.fake()->bothify('Ref ??-####'),
            'condition' => fake()->randomElement(['New', 'Like New', 'Good', 'Fair', 'Parts/Repair']),
            'starting_bid_yen' => $startingBid,
            'current_bid_yen' => $currentBid,
            'bid_count' => fake()->numberBetween(0, 58),
            'status' => fake()->randomElement(['active', 'ending_soon', 'closed']),
            'starts_at' => now()->subMinutes(fake()->numberBetween(30, 60 * 24)),
            'ends_at' => $endsAt,
            'seller_name' => fake()->userName(),
            'seller_rating' => fake()->randomFloat(2, 90, 100),
            'thumbnail_url' => null,
            'image_urls' => [],
            'raw' => null,
            'last_synced_at' => now(),
        ];
    }
}
