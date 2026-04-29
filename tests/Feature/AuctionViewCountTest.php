<?php

use App\Models\Auction;

it('increments auction view count when the public auction page is visited', function () {
    $auction = Auction::factory()->create();

    $this->get(route('auctions.show', $auction))
        ->assertStatus(200);

    expect($auction->fresh()->view_count)->toBe(1);
});
