<?php

use App\Models\Auction;
use App\Models\User;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user auction detail shows image carousel fallback and proxy bid summary', function () {
    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 100000]);

    $otherUser = User::factory()->create();
    $otherUser->wallet->update(['balance_yen' => 100000]);

    $auction = Auction::factory()->create([
        'status' => 'active',
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 0,
        'thumbnail_url' => 'https://example.com/image.jpg',
        'image_urls' => [],
    ]);

    app(BiddingService::class)->placeBid($otherUser, $auction, 5000);
    app(BiddingService::class)->placeBid($user, $auction, 6000);

    $response = $this->actingAs($user, 'user')->get(route('user.auctions.show', $auction));

    $response->assertOk();
    $response->assertSeeText('Current Price: ¥');
    $response->assertSeeText('Highest Max Bid: ¥');
    $response->assertSee('https://example.com/image.jpg');
    $response->assertSeeText('You are the current top bidder with a maximum proxy bid');
});
