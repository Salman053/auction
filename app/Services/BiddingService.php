<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\ShippingRate;
use App\Models\User;
use App\Notifications\AdminNewBidNotification;
use App\Notifications\BidPlacedNotification;
use App\Notifications\LowBalanceNotification;
use App\Notifications\OutbidNotification;
use Illuminate\Support\Facades\DB;
use App\Enums\UserRole;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\SettingService;

class BiddingService
{
    private const CANCELLATION_WINDOW_HOURS = 1;

    public function __construct(private ProxyBiddingService $proxyBiddingService) {}

    public function placeBid(User $user, Auction $auction, int $maxAmountYen, ?int $shippingRateId = null): array
    {
        if ($maxAmountYen <= 0) {
            throw ValidationException::withMessages([
                'amount_yen' => 'Bid amount must be greater than zero.',
            ]);
        }

        if (! in_array($auction->status, ['active', 'ending_soon'], true)) {
            throw ValidationException::withMessages([
                'auction' => 'Auction is not open for bidding.',
            ]);
        }

        if ($user->wallet === null) {
            throw ValidationException::withMessages([
                'wallet' => 'Wallet not found.',
            ]);
        }

        return DB::transaction(function () use ($user, $auction, $maxAmountYen, $shippingRateId): array {
            $auction = Auction::query()->lockForUpdate()->findOrFail($auction->id);
            $wallet = $user->wallet()->lockForUpdate()->firstOrFail();

            if ($shippingRateId) {
                $shippingRate = ShippingRate::findOrFail($shippingRateId);
                $user->update(['shipping_rate_id' => $shippingRate->id]);
            } else {
                $shippingRate = $user->shippingRate;
            }

            $destinationFee = (int) ($shippingRate?->fee_yen ?? 0);

            if ($auction->ends_at !== null && $auction->ends_at->isPast()) {
                throw ValidationException::withMessages([
                    'auction' => 'Auction has already ended.',
                ]);
            }

            // Check Bidding Capacity
            $multiplierPercent = (int) ($user->bidding_multiplier_percent ?? 0);
            if ($multiplierPercent <= 0) {
                $multiplierPercent = app(SettingService::class)->getInt(
                    SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY,
                    500,
                );
            }

            $capacityYen = (int) floor(((int) $wallet->balance_yen) * ($multiplierPercent / 100));

            $existingBid = Bid::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            $previouslyLockedForThisAuction = $existingBid ? $existingBid->locked_amount_yen : 0;

            $totalCommitmentYen = $maxAmountYen + $destinationFee + (int) ($auction->shipping_fee_yen ?? 0);
            $newAdditionalLockNeeded = $totalCommitmentYen - $previouslyLockedForThisAuction;

            $availableCapacityYen = max(0, $capacityYen - (int) $wallet->locked_balance_yen - (int) $wallet->withdrawal_locked_yen);

            if ($newAdditionalLockNeeded > $availableCapacityYen) {
                throw ValidationException::withMessages([
                    'amount_yen' => 'Bid (including shipping) exceeds your available bidding capacity of ¥'.number_format($availableCapacityYen + $previouslyLockedForThisAuction),
                ]);
            }

            // Call Proxy Bidding Logic
            $result = $this->proxyBiddingService->process($auction, $user, $maxAmountYen);

            if ($result['status'] === 'no_change') {
                return $result;
            }

            if ($result['status'] === 'failed_outbid') {
                // The current user was immediately outbid by a proxy.
                // We don't lock their funds, but we update the auction price which was pushed up.
                $auction->update([
                    'current_bid_yen' => $result['bid']->amount_yen,
                    'bid_count' => $auction->bids()->count(),
                ]);
                return $result;
            }

            // Update user wallet lock (for success or updated max bid)
            if ($newAdditionalLockNeeded != 0) {
                $wallet->increment('locked_balance_yen', $newAdditionalLockNeeded);
            }

            // Sync locked amount in the bid record
            if ($result['bid']) {
                $result['bid']->update(['locked_amount_yen' => $totalCommitmentYen]);
            }

            // If we outbid someone, unlock their balance and notify them
            if ($result['status'] === 'success' && isset($result['outbid_user'])) {
                $outbidUser = $result['outbid_user'];
                $outbidBid = $result['previous_bid'];

                $outbidUser->wallet()->increment('locked_balance_yen', -$outbidBid->locked_amount_yen);
                $outbidUser->notify(new OutbidNotification($auction, $result['bid']->amount_yen));
            }

            // Sync auction stats
            $auction->update([
                'current_bid_yen' => $result['bid']->amount_yen,
                'bid_count' => $auction->bids()->count(),
            ]);

            // Notify the bidder
            $user->notify(new BidPlacedNotification($auction, $result['bid']));

            // Notify all administrators
            $admins = User::where('role', UserRole::Admin->value)->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNewBidNotification($auction, $result['bid'], $user));
            }

            // Check for low balance
            $balance = (int) ($wallet->balance_yen ?? 0);
            $threshold = 5000;
            if ($balance < $threshold) {
                $user->notify(new LowBalanceNotification($balance, $threshold));
            }

            return $result;
        });
    }

    public function cancelBid(User $user, Bid $bid): Bid
    {
        if ((int) $bid->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'bid' => 'You can only cancel your own bids.',
            ]);
        }

        if ($bid->status !== 'active') {
            throw ValidationException::withMessages([
                'bid' => 'Only active bids can be cancelled.',
            ]);
        }

        $createdAt = $bid->created_at?->toImmutable();
        if ($createdAt === null || $createdAt->isBefore(now()->subHours(self::CANCELLATION_WINDOW_HOURS))) {
            throw ValidationException::withMessages([
                'bid' => 'Bids can only be cancelled within '.self::CANCELLATION_WINDOW_HOURS.' '.Str::plural('hour', self::CANCELLATION_WINDOW_HOURS).' of placement.',
            ]);
        }

        return DB::transaction(function () use ($user, $bid): Bid {
            /** @var Bid $bid */
            $bid = Bid::query()->lockForUpdate()->findOrFail($bid->id);

            if ($bid->status !== 'active') {
                throw ValidationException::withMessages([
                    'bid' => 'This bid is no longer active.',
                ]);
            }

            $auction = Auction::query()->lockForUpdate()->findOrFail($bid->auction_id);

            if (! in_array($auction->status, ['active', 'ending_soon'], true)) {
                throw ValidationException::withMessages([
                    'auction' => 'Auction is not open for bid cancellation.',
                ]);
            }

            if ($auction->ends_at !== null && $auction->ends_at->isPast()) {
                throw ValidationException::withMessages([
                    'auction' => 'Auction has already ended.',
                ]);
            }

            $wallet = $user->wallet()->lockForUpdate()->firstOrFail();

            $locked = (int) $bid->locked_amount_yen;

            $bid->forceFill([
                'status' => 'cancelled',
                'canceled_at' => now(),
            ])->save();

            $wallet->forceFill([
                'locked_balance_yen' => max(0, (int) $wallet->locked_balance_yen - $locked),
            ])->save();

            /** @var Bid|null $nextBid */
            $nextBid = Bid::query()
                ->where('auction_id', $auction->id)
                ->where('status', 'outbid')
                ->lockForUpdate()
                ->orderByDesc('max_amount_yen')
                ->orderBy('created_at')
                ->first();

            if ($nextBid === null) {
                $auction->forceFill([
                    'current_bid_yen' => (int) $auction->starting_bid_yen,
                ])->save();

                return $bid;
            }

            // Promote the best remaining max-bid back to active and recompute current price.
            $nextBid->forceFill(['status' => 'active'])->save();

            /** @var Bid|null $secondBest */
            $secondBest = Bid::query()
                ->where('auction_id', $auction->id)
                ->whereIn('status', ['outbid', 'active'])
                ->whereKeyNot($nextBid->id)
                ->lockForUpdate()
                ->orderByDesc('max_amount_yen')
                ->orderBy('created_at')
                ->first();

            $increment = 100;
            $starting = max((int) $auction->starting_bid_yen, (int) $auction->current_bid_yen);
            $target = $secondBest ? ((int) $secondBest->max_amount_yen + $increment) : $starting;
            $newAmount = min((int) $nextBid->max_amount_yen, max($starting, $target));

            $nextBid->forceFill([
                'amount_yen' => $newAmount,
            ])->save();

            $auction->forceFill([
                'current_bid_yen' => $newAmount,
            ])->save();

            return $bid;
        });
    }
}
