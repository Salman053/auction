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
        'current_bid_yen' => 1000,
        'thumbnail_url' => 'https://example.com/thumb.jpg',
        'image_urls' => [
            'https://example.com/skipped1.jpg',
            'https://example.com/skipped2.jpg',
            'https://example.com/visible.jpg',
        ],
    ]);

    app(BiddingService::class)->placeBid($otherUser, $auction, 5000);
    app(BiddingService::class)->placeBid($user, $auction, 6000);

    $response = $this->actingAs($user, 'user')->get(route('user.auctions.show', $auction));

    $response->assertOk();
    $response->assertSeeText('Current Bid');
    $response->assertSeeText('Your Max Bid');
    $response->assertSee('https://example.com/visible.jpg');
    $response->assertDontSee('https://example.com/skipped1.jpg');
    $response->assertSeeText('You are the current top bidder');
});
