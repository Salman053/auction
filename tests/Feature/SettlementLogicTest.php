<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Services\AuctionSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('settlement detects yahoo outbid state and transitions correctly', function () {
    $user = User::factory()->create();
    $wallet = $user->wallet;
    $wallet->update(['locked_balance_yen' => 12000]); // Match the bid lock

    $auction = Auction::factory()->create([
        'status' => 'active',
        'current_bid_yen' => 15000, // External outbid price
        'ends_at' => now()->subMinute(),
    ]);

    $bid = Bid::factory()->create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'amount_yen' => 10000,
        'max_amount_yen' => 12000, // User's max is lower than Yahoo's current price
        'status' => 'active',
        'locked_amount_yen' => 12000,
    ]);

    $service = app(AuctionSettlementService::class);
    $service->settleAuction($auction);

    $auction->refresh();
    $bid->refresh();
    $wallet->refresh();

    expect($auction->status)->toBe('ended_outbid_on_yahoo');
    expect($bid->status)->toBe('lost');
    expect($bid->locked_amount_yen)->toBe(0);
    expect($wallet->locked_balance_yen)->toBe(0);
});

test('settlement handles race condition where reconciliation already unlocked funds', function () {
    $user = User::factory()->create();
    $wallet = $user->wallet;
    $wallet->update(['locked_balance_yen' => 0]); // Already unlocked by reconciliation

    $auction = Auction::factory()->create([
        'status' => 'active',
        'current_bid_yen' => 15000,
        'ends_at' => now()->subMinute(),
    ]);

    $bid = Bid::factory()->create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'status' => 'outbid', // Already outbid by reconciliation
        'locked_amount_yen' => 0, // Already 0
    ]);

    $service = app(AuctionSettlementService::class);
    $service->settleAuction($auction);

    $bid->refresh();
    $wallet->refresh();

    expect($bid->status)->toBe('lost');
    expect($wallet->locked_balance_yen)->toBe(0); // Should still be 0, not negative
});

test('settlement transitions to ended_no_bids when no bids exist', function () {
    $auction = Auction::factory()->create([
        'status' => 'active',
        'ends_at' => now()->subMinute(),
    ]);

    $service = app(AuctionSettlementService::class);
    $service->settleAuction($auction);

    expect($auction->refresh()->status)->toBe('ended_no_bids');
});
