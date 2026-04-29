<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Services\BiddingService;

it('allows a user to cancel their active bid within 1 hour and releases locked funds', function () {
    $user = User::factory()->create([
        'password' => 'password',
        'bidding_multiplier_percent' => 100,
    ]);

    $user->wallet()->update([
        'balance_yen' => 100_000,
        'locked_balance_yen' => 0,
        'withdrawal_locked_yen' => 0,
    ]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'cancel123',
        'title' => 'Cancel Auction',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $service = app(BiddingService::class);
    $service->placeBid($user, $auction, 10_000);

    $user->refresh();
    expect((int) $user->wallet->locked_balance_yen)->toBe(10_000);

    /** @var Bid $bid */
    $bid = Bid::query()->where('auction_id', $auction->id)->where('user_id', $user->id)->where('status', 'active')->firstOrFail();

    $service->cancelBid($user, $bid);

    $user->refresh();
    $bid->refresh();
    $auction->refresh();

    expect($bid->status)->toBe('cancelled');
    expect((int) $user->wallet->locked_balance_yen)->toBe(0);
    expect((int) $auction->current_bid_yen)->toBe((int) $auction->starting_bid_yen);
});

it('prevents cancelling a bid after 1 hour', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'cancel456',
        'title' => 'Cancel Auction 2',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $bid = Bid::query()->create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'amount_yen' => 1_000,
        'max_amount_yen' => 1_000,
        'status' => 'active',
        'placed_via' => 'manual',
    ]);

    $bid->forceFill([
        'created_at' => now()->subHours(5),
        'updated_at' => now()->subHours(5),
    ])->save();

    app(BiddingService::class)->cancelBid($user, $bid);
})->throws(\Illuminate\Validation\ValidationException::class);

it('returns a friendly error when trying to cancel an expired bid from the bid cancel route', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $auction = Auction::query()->create([
        'yahoo_auction_id' => 'cancel789',
        'title' => 'Cancel Auction 3',
        'starting_bid_yen' => 0,
        'current_bid_yen' => 0,
        'bid_count' => 0,
        'status' => 'active',
        'ends_at' => now()->addHour(),
        'last_synced_at' => now(),
    ]);

    $bid = Bid::query()->create([
        'auction_id' => $auction->id,
        'user_id' => $user->id,
        'amount_yen' => 1_000,
        'max_amount_yen' => 1_000,
        'status' => 'active',
        'placed_via' => 'manual',
    ]);

    $bid->forceFill([
        'created_at' => now()->subHours(5),
        'updated_at' => now()->subHours(5),
    ])->save();

    $response = $this->actingAs($user, 'user')
        ->from(route('user.bids.index'))
        ->post(route('user.bids.cancel', $bid));

    $response->assertRedirect(route('user.bids.index'));
    $response->assertSessionHas('error', 'Bids can only be cancelled within 1 hour of placement.');

    $bid->refresh();
    expect($bid->status)->toBe('active');
});
