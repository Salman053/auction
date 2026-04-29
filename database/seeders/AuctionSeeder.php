<?php

namespace Database\Seeders;

use App\Models\Auction;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auction::factory()->count(48)->create();
    }
}
