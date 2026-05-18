<?php

use App\Jobs\SyncAuctionDetails;
use App\Models\Auction;
use App\Models\User;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

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
            'https://example.com/banner.jpg',
            'https://example.com/promo.jpg',
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
    $response->assertDontSee('https://example.com/banner.jpg');
    $response->assertSeeText('You are the current top bidder');
});

test('getUpdates endpoint returns real-time properties and user bid status', function () {
    Queue::fake();

    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 100000]);

    $auction = Auction::factory()->create([
        'status' => 'active',
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 2000,
        'bid_count' => 5,
        'ends_at' => now()->addDay(),
        'last_synced_at' => now()->subMinutes(30), // stale!
    ]);

    app(BiddingService::class)->placeBid($user, $auction, 5000);

    $response = $this->actingAs($user, 'user')->get(route('user.auctions.updates', $auction));

    $response->assertOk();
    $response->assertJsonStructure([
        'current_bid_yen',
        'bid_count',
        'highest_active_bid_id',
        'ends_at',
        'ends_at_human',
        'bids_html',
        'user_bid_status' => [
            'has_bid',
            'is_top_bidder',
            'user_max_bid',
        ],
    ]);

    // Verify it triggers a background sync because last_synced_at was stale
    Queue::assertPushed(SyncAuctionDetails::class);
});
