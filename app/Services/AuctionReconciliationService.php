<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Notifications\OutbidNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionReconciliationService
{
    /**
     * Reconcile internal bids with external Yahoo price.
     * This should be called after an auction is updated from Yahoo.
     */
    public function reconcile(Auction $auction): void
    {
        $yahooPrice = (int) $auction->current_bid_yen;

        // Find the active internal high bid
        $activeBid = Bid::where('auction_id', $auction->id)
            ->where('status', 'active')
            ->orderByDesc('max_amount_yen')
            ->first();

        if (! $activeBid) {
            return;
        }

        // If Yahoo price is greater than our internal high bidder's maximum amount,
        // it means they have been outbid on Yahoo by an external user.
        if ($yahooPrice > (int) $activeBid->max_amount_yen) {
            DB::transaction(function () use ($auction, $activeBid, $yahooPrice) {
                // Double check status inside transaction
                $activeBid = Bid::query()->lockForUpdate()->find($activeBid->id);
                if (! $activeBid || $activeBid->status !== 'active') {
                    return;
                }

                $user = $activeBid->user;
                $wallet = $user->wallet()->lockForUpdate()->first();

                // 1. Transition bid to outbid
                $activeBid->update([
                    'status' => 'outbid',
                    'amount_yen' => $yahooPrice, // Reflect the actual outbid price
                ]);

                // 2. Unlock funds immediately
                if ($wallet) {
                    $wallet->decrement('locked_balance_yen', $activeBid->locked_amount_yen);
                    $activeBid->update(['locked_amount_yen' => 0]);
                }

                // 3. Notify user
                $user->notify(new OutbidNotification($auction, $yahooPrice));

                Log::info("Reconciliation: User #{$user->id} outbid on Auction #{$auction->id} (Yahoo Price: ¥{$yahooPrice} > Max: ¥{$activeBid->max_amount_yen})");
            });
        }
    }
}
