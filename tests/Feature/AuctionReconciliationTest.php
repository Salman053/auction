<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AuctionReconciliationService;
use App\Services\AuctionSettlementService;
use App\Services\BiddingService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OutbidNotification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    app(SettingService::class)->setInt('default_bidding_multiplier_percent', 500);
});

test('auction reconciliation marks user as outbid and unlocks funds when yahoo price exceeds max bid', function () {
    Notification::fake();

    $user = User::factory()->create();
    $user->forceFill(['bidding_multiplier_percent' => 500])->save();
    
    // User factory creates a wallet. Update it instead of creating a new one.
    $user->wallet->update(['balance_yen' => 100000, 'locked_balance_yen' => 0]);
    
    $auction = Auction::factory()->create([
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 1000,
        'status' => 'active'
    ]);

    // Place a bid
    $biddingService = app(BiddingService::class);
    $biddingService->placeBid($user, $auction, 5000);

    $bid = Bid::where('user_id', $user->id)->first();
    expect($bid->status)->toBe('active');
    expect($user->wallet->refresh()->locked_balance_yen)->toBe(5000);

    // Simulate Yahoo price update exceeding max bid
    $auction->update(['current_bid_yen' => 6000]);

    // Run reconciliation
    app(AuctionReconciliationService::class)->reconcile($auction);

    $bid->refresh();
    expect($bid->status)->toBe('outbid');
    expect($bid->locked_amount_yen)->toBe(0);
    expect($user->wallet->refresh()->locked_balance_yen)->toBe(0);

    Notification::assertSentTo($user, OutbidNotification::class);
});

test('settlement correctly handles users outbid on yahoo', function () {
    $user = User::factory()->create();
    $user->wallet->update([
        'balance_yen' => 100000,
        'locked_balance_yen' => 0,
        'withdrawal_locked_yen' => 0
    ]);
    
    $auction = Auction::factory()->create([
        'starting_bid_yen' => 1000,
        'current_bid_yen' => 6000, // Yahoo price higher than user's max
        'status' => 'active',
        'ends_at' => now()->subMinute(),
    ]);

    // Manually create an "active" bid that was outbid by Yahoo but not reconciled yet
    $bid = Bid::create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'amount_yen' => 5000,
        'max_amount_yen' => 5000,
        'status' => 'active',
        'locked_amount_yen' => 5000,
    ]);
    $user->wallet->update(['locked_balance_yen' => 5000]);

    // Run settlement
    app(AuctionSettlementService::class)->settleEndedAuctions();

    $bid->refresh();
    expect($bid->status)->toBe('lost');
    expect($bid->locked_amount_yen)->toBe(0);
    expect($user->wallet->refresh()->locked_balance_yen)->toBe(0);
    expect($auction->refresh()->status)->toBe('ended_outbid_on_yahoo');
});

test('bidding capacity takes pending withdrawals into account correctly', function () {
    $user = User::factory()->create();
    $user->forceFill(['bidding_multiplier_percent' => 500])->save();
    
    $user->wallet->update([
        'balance_yen' => 10000, 
        'withdrawal_locked_yen' => 4000,
        'locked_balance_yen' => 0
    ]);

    // Effective balance = 10000 - 4000 = 6000
    // Capacity = 6000 * 5 = 30000
    
    $auction = Auction::factory()->create([
        'starting_bid_yen' => 1000,
        'status' => 'active'
    ]);

    $biddingService = app(BiddingService::class);

    // Should be able to bid up to 30000
    $biddingService->placeBid($user, $auction, 30000);
    expect(Bid::where('user_id', $user->id)->count())->toBe(1);

    // Should NOT be able to bid 31000
    expect(fn() => $biddingService->placeBid($user, $auction, 31000))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});
