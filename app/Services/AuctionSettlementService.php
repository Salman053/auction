<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\ShippingRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionSettlementService
{
    public function settleEndedAuctions(): int
    {
        $auctions = Auction::shouldClose()->get();
        $count = 0;

        foreach ($auctions as $auction) {
            try {
                $this->settleAuction($auction);
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to settle auction {$auction->id}: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function settleAuction(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction = Auction::query()->lockForUpdate()->find($auction->id);
            if (! $auction || $auction->status !== 'active') {
                return;
            }

            // Get all bids to settle
            $bids = Bid::where('auction_id', $auction->id)
                ->whereIn('status', ['active', 'outbid'])
                ->get();

            $highestBid = $bids->where('status', 'active')->sortByDesc('amount_yen')->first();

            foreach ($bids as $bid) {
                $user = $bid->user;
                $wallet = $user->wallet()->lockForUpdate()->first();

                if (! $wallet) {
                    continue;
                }

                if ($highestBid && $bid->id === $highestBid->id) {
                    // Winner
                    $bid->update(['status' => 'won']);

                    $shippingRate = $bid->user->shippingRate;
                    $destinationFee = (int) ($shippingRate?->fee_yen ?? 0);
                    $totalCost = $bid->amount_yen + $destinationFee + (int) ($auction->shipping_fee_yen ?? 0);

                    // Unlock the EXACT amount they had locked
                    $wallet->decrement('locked_balance_yen', $bid->locked_amount_yen);

                    // Deduct the ACTUAL cost (Bid + Shipping) from balance
                    $wallet->decrement('balance_yen', $totalCost);

                    // Create transaction record
                    $wallet->transactions()->create([
                        'type' => 'payment',
                        'amount_yen' => $totalCost,
                        'memo' => "Payment for winning Auction #{$auction->yahoo_auction_id}: {$auction->title}",
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]);

                    // Update auction with winner info
                    $auction->update([
                        'status' => 'finished',
                        'winner_user_id' => $user->id,
                        'winning_bid_id' => $bid->id,
                    ]);

                    // Notify winner
                    try {
                        $user->notify(new \App\Notifications\AuctionWonNotification($auction, $bid->amount_yen));
                    } catch (\Exception $e) {
                        Log::error("Failed to notify winner of auction {$auction->id}: " . $e->getMessage());
                    }
                } else {
                    // Loser
                    $bid->update(['status' => 'lost']);

                    // Unlock their exact locked amount
                    $wallet->decrement('locked_balance_yen', $bid->locked_amount_yen);
                }
            }

            if (! $highestBid) {
                $auction->update(['status' => 'ended_no_bids']);
            }
        });
    }
}
