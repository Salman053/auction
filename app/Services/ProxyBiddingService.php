<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;

class ProxyBiddingService
{
    public function __construct(private SettingService $settingService) {}

    /**
     * Process a new bid and return the result.
     * 
     * Logic:
     * 1. If no existing bids, current bid = starting price, max bid = user's max.
     * 2. If user is already the highest bidder, update their max bid. Current price stays same.
     * 3. If new max bid > current max bid:
     *    - Previous high bidder status = outbid.
     *    - New current price = previous max bid + increment.
     *    - New high bidder max = user's max.
     * 4. If new max bid <= current max bid:
     *    - New user status = outbid immediately.
     *    - Current price = new max bid + increment (not exceeding current max).
     */
    public function process(Auction $auction, User $user, int $maxAmountYen): array
    {
        $currentHighestBid = Bid::where('auction_id', $auction->id)
            ->where('status', 'active')
            ->orderByDesc('max_amount_yen')
            ->orderBy('created_at')
            ->first();

        $increment = $this->getIncrement($currentHighestBid?->amount_yen ?? $auction->starting_bid_yen);

        // 1. First Bid
        if (!$currentHighestBid) {
            $startingPrice = max((int) $auction->starting_bid_yen, (int) $auction->current_bid_yen);
            $bid = $this->createBid($auction, $user, $startingPrice, $maxAmountYen);

            return ['status' => 'success', 'bid' => $bid, 'outbid_user' => null];
        }

        // 2. Same user updating their max bid
        if ($user->id === $currentHighestBid->user_id) {
            if ($maxAmountYen <= $currentHighestBid->max_amount_yen) {
                return ['status' => 'no_change', 'bid' => $currentHighestBid, 'outbid_user' => null];
            }
            $currentHighestBid->update([
                'max_amount_yen' => $maxAmountYen,
            ]);

            return ['status' => 'updated', 'bid' => $currentHighestBid, 'outbid_user' => null];
        }

        // 3. New user outbidding existing max
        if ($maxAmountYen > $currentHighestBid->max_amount_yen) {
            $outbidUser = $currentHighestBid->user;
            $previousMax = (int) $currentHighestBid->max_amount_yen;
            $currentHighestBid->update(['status' => 'outbid']);

            // Recalculate increment based on the price we are outbidding
            $dynamicIncrement = $this->getIncrement($previousMax);
            $newAmount = $previousMax + $dynamicIncrement;
            
            if ($newAmount > $maxAmountYen) {
                $newAmount = $maxAmountYen;
            }

            $bid = $this->createBid($auction, $user, $newAmount, $maxAmountYen);

            return ['status' => 'success', 'bid' => $bid, 'outbid_user' => $outbidUser, 'previous_bid' => $currentHighestBid];
        }

        // 4. New user bid is lower than or equal to existing max
        // Existing user stays high bidder, but price is pushed up
        $outbidBid = $this->createBid($auction, $user, $maxAmountYen, $maxAmountYen);
        $outbidBid->update(['status' => 'outbid']);

        $dynamicIncrement = $this->getIncrement($maxAmountYen);
        $newAmount = min((int) $currentHighestBid->max_amount_yen, $maxAmountYen + $dynamicIncrement);
        $currentHighestBid->update(['amount_yen' => $newAmount]);

        return [
            'status' => 'failed_outbid', 
            'bid' => $currentHighestBid, 
            'outbid_user' => null, 
            'new_bid' => $outbidBid
        ];
    }

    private function getIncrement(int $currentPrice): int
    {
        if ($currentPrice < 1000) return 10;
        if ($currentPrice < 5000) return 100;
        if ($currentPrice < 10000) return 250;
        if ($currentPrice < 50000) return 500;
        return 1000;
    }

    private function createBid(Auction $auction, User $user, int $amount, int $maxAmount): Bid
    {
        return Bid::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'amount_yen' => $amount,
            'max_amount_yen' => $maxAmount,
            'status' => 'active',
            'placed_via' => 'manual',
        ]);
    }
}
