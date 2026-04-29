<?php

use App\Models\Auction;
use App\Models\User;
use App\Services\BiddingService;
use Illuminate\Validation\ValidationException;

it('locks only the delta when a user raises their own highest bid', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $user->wallet()->update([
        'balance_yen' => 100_000,
        'locked_balance_yen' => 0,
    ]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'test123',
        'title' => 'Test Auction',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $service = app(BiddingService::class);

    $service->placeBid($user, $auction, 1_000);
    $user->refresh();
    $auction->refresh();

    expect((int) $user->wallet->locked_balance_yen)->toBe(1_000);
    expect((int) $auction->current_bid_yen)->toBe(0); // Starting bid was 0, no other bidders

    $service->placeBid($user, $auction, 1_500);
    $user->refresh();
    $auction->refresh();

    expect((int) $user->wallet->locked_balance_yen)->toBe(1_500);
    expect((int) $auction->current_bid_yen)->toBe(0); // Price shouldn't increase when raising own limit
    expect($auction->bids()->where('status', 'active')->count())->toBe(1);
});

it('releases the previous highest bidders lock when outbid', function () {
    $userA = User::factory()->create(['password' => 'password']);
    $userB = User::factory()->create(['password' => 'password']);

    $userA->wallet()->update(['balance_yen' => 100_000, 'locked_balance_yen' => 0]);
    $userB->wallet()->update(['balance_yen' => 100_000, 'locked_balance_yen' => 0]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'test456',
        'title' => 'Test Auction 2',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $service = app(BiddingService::class);

    $service->placeBid($userA, $auction, 1_000);
    $service->placeBid($userB, $auction, 2_000);

    $userA->refresh();
    $userB->refresh();
    $auction->refresh();

    expect((int) $userA->wallet->locked_balance_yen)->toBe(0);
    expect((int) $userB->wallet->locked_balance_yen)->toBe(2_000);
    expect((int) $auction->current_bid_yen)->toBe(1_100); // User A max (1000) + 100 increment
});

it('prevents bidding into capacity reserved for withdrawals', function () {
    $user = User::factory()->create([
        'password' => 'password',
        'bidding_multiplier_percent' => 100,
    ]);

    $user->wallet()->update([
        'balance_yen' => 10_000,
        'locked_balance_yen' => 0,
        'withdrawal_locked_yen' => 9_500,
    ]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'test789',
        'title' => 'Test Auction 3',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $service = app(BiddingService::class);

    $service->placeBid($user, $auction, 600);
})->throws(ValidationException::class);
