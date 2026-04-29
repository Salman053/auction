<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can place a standard bid and it locks the max amount', function () {
    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 100000]);
    $auction = Auction::factory()->create([
        'status' => 'active',
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 0,
        'bid_count' => 0,
    ]);

    $biddingService = app(BiddingService::class);

    $result = $biddingService->placeBid($user, $auction, 5000); // Max bid ¥5000

    expect($result['status'])->toBe('success');
    expect($result['bid']->amount_yen)->toBe(1000); // First bid starts at starting price
    expect($result['bid']->max_amount_yen)->toBe(5000);

    $user->wallet->refresh();
    expect($user->wallet->locked_balance_yen)->toBe(5000);

    $auction->refresh();
    expect($auction->current_bid_yen)->toBe(1000);
});

test('proxy bidding automatically increments when a second user bids', function () {
    $user1 = User::factory()->create();
    $user1->wallet->update(['balance_yen' => 100000]);

    $user2 = User::factory()->create();
    $user2->wallet->update(['balance_yen' => 100000]);

    $auction = Auction::factory()->create(['status' => 'active', 'starting_bid_yen' => 1000, 'current_bid_yen' => 0]);

    $biddingService = app(BiddingService::class);

    // User 1 bids ¥5000 max
    $biddingService->placeBid($user1, $auction, 5000);

    $auction->refresh();
    expect($auction->current_bid_yen)->toBe(1000);

    // User 2 bids ¥2000 max
    $result = $biddingService->placeBid($user2, $auction, 2000);

    // User 2 should be outbid immediately by User 1's proxy
    expect($result['status'])->toBe('failed_outbid');

    $auction->refresh();
    // User 1 stays winner, bid becomes User 2 max + 100
    expect($auction->current_bid_yen)->toBe(2100);

    $highestBid = Bid::where('auction_id', $auction->id)->where('status', 'active')->first();
    expect($highestBid->user_id)->toBe($user1->id);
    expect($highestBid->amount_yen)->toBe(2100);
});

test('user can outbid a proxy bid if their max is higher', function () {
    $user1 = User::factory()->create();
    $user1->wallet->update(['balance_yen' => 100000]);

    $user2 = User::factory()->create();
    $user2->wallet->update(['balance_yen' => 100000]);

    $auction = Auction::factory()->create(['status' => 'active', 'starting_bid_yen' => 1000]);

    $biddingService = app(BiddingService::class);

    // User 1 bids ¥2000 max
    $biddingService->placeBid($user1, $auction, 2000);

    // User 2 bids ¥5000 max
    $result = $biddingService->placeBid($user2, $auction, 5000);

    expect($result['status'])->toBe('success');
    expect($result['outbid_user']->id)->toBe($user1->id);

    $auction->refresh();
    // User 2 wins at User 1 max + 100
    expect($auction->current_bid_yen)->toBe(2100);

    $highestBid = Bid::where('auction_id', $auction->id)->where('status', 'active')->first();
    expect($highestBid->user_id)->toBe($user2->id);
    expect($highestBid->amount_yen)->toBe(2100);

    // User 1 should have their ¥2000 unlocked
    $user1->wallet->refresh();
    expect($user1->wallet->locked_balance_yen)->toBe(0);
});
