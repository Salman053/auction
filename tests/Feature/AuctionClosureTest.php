<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Notifications\AuctionWonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('auction is closed and winner determined when time ends', function () {
    Notification::fake();

    $user = User::factory()->create();
    $user->wallet->update([
        'balance_yen' => 100000,
        'locked_balance_yen' => 5000,
    ]);

    $auction = Auction::factory()->create([
        'status' => 'active',
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 5000,
        'bid_count' => 1,
        'ends_at' => now()->subMinute(), // Ended 1 minute ago
    ]);

    $bid = Bid::factory()->create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'amount_yen' => 5000,
        'max_amount_yen' => 10000,
        'status' => 'active',
        'locked_amount_yen' => 5000,
    ]);

    Artisan::call('auctions:close');

    $auction->refresh();
    expect($auction->status)->toBe('finished');
    expect($auction->winner_user_id)->toBe($user->id);
    expect($auction->winning_bid_id)->toBe($bid->id);

    $user->wallet->refresh();
    // Balance was 100k, bid was 5k, should be 95k
    expect($user->wallet->balance_yen)->toBe(95000);
    // Locked balance was 5k (from the bid), should be 0 now (deducted + unlocked)
    // Actually, CloseEndedAuctions deducts amount_yen from both balance and locked_balance
    expect($user->wallet->locked_balance_yen)->toBe(0);

    Notification::assertSentTo($user, AuctionWonNotification::class);
});

test('multiple auctions are closed correctly', function () {
    $auction1 = Auction::factory()->create(['status' => 'active', 'ends_at' => now()->subMinutes(10)]);
    $auction2 = Auction::factory()->create(['status' => 'active', 'ends_at' => now()->subMinutes(5)]);
    $auction3 = Auction::factory()->create(['status' => 'active', 'ends_at' => now()->addMinutes(10)]); // Not ended yet

    Artisan::call('auctions:close');

    expect($auction1->refresh()->status)->toBe('ended_no_bids');
    expect($auction2->refresh()->status)->toBe('ended_no_bids');
    expect($auction3->refresh()->status)->toBe('active');
});
