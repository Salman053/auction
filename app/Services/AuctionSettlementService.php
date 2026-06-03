<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Notifications\AuctionWonNotification;
use App\Notifications\AdminAuctionSettledNotification;
use App\Models\User;
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
                Log::error("Failed to settle auction {$auction->id} (#{$auction->yahoo_auction_id}): ".$e->getMessage(), [
                    'exception' => $e,
                    'auction_status' => $auction->status,
                ]);
            }
        }

        return $count;
    }

    public function settleAuction(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction = Auction::query()->lockForUpdate()->find($auction->id);
            if (! $auction || ! in_array($auction->status, ['active', 'closed'])) {
                return;
            }

            // Get all bids to settle (lock them to prevent race conditions with Reconciliation)
            $bids = Bid::where('auction_id', $auction->id)
                ->whereIn('status', ['active', 'outbid'])
                ->lockForUpdate()
                ->get();

            $highestBid = $bids->where('status', 'active')->sortByDesc('amount_yen')->first();

            // BUG-001 Fix: Verify if the internal high bidder was actually outbid on Yahoo
            $isYahooOutbid = false;
            if ($highestBid && (int) $auction->current_bid_yen > (int) $highestBid->max_amount_yen) {
                $isYahooOutbid = true;
                Log::info("Settlement: User #{$highestBid->user_id} was outbid on Yahoo for Auction #{$auction->id} (Yahoo: ¥{$auction->current_bid_yen} > Max: ¥{$highestBid->max_amount_yen})");
            }

            foreach ($bids as $bid) {
                $user = $bid->user;
                $wallet = $user->wallet()->lockForUpdate()->first();

                if (! $wallet) {
                    continue;
                }

                // Refresh bid to get latest locked_amount_yen in case it was changed by Reconciliation
                $bid->refresh();

                if (! $isYahooOutbid && $highestBid && $bid->id === $highestBid->id && $bid->status === 'active') {
                    // Winner
                    $bid->update(['status' => 'won']);

                    $shippingRate = $bid->shippingRate ?? $bid->user->shippingRate;
                    $destinationFee = (int) ($shippingRate?->fee_yen ?? 0);
                    $totalCost = $bid->amount_yen + $destinationFee;

                    // Unlock the EXACT amount they had locked (if any still remains)
                    if ($bid->locked_amount_yen > 0) {
                        $wallet->decrement('locked_balance_yen', $bid->locked_amount_yen);
                        $bid->update(['locked_amount_yen' => 0]);
                    }

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
                        $user->notify(new AuctionWonNotification($auction, $bid->amount_yen));
                    } catch (\Exception $e) {
                        Log::error("Failed to notify winner of auction {$auction->id}: ".$e->getMessage());
                    }
                } else {
                    // Loser
                    $bid->update(['status' => 'lost']);

                    // Unlock their exact locked amount (if any still remains)
                    if ($bid->locked_amount_yen > 0) {
                        $wallet->decrement('locked_balance_yen', $bid->locked_amount_yen);
                        $bid->update(['locked_amount_yen' => 0]);
                    }
                }
            }

            // Determine final status and winner details for admin notification
            $finalStatus = $auction->status; // Initialize with current status
            $winner = null;
            $winningBidAmount = null;

            if ($auction->status === 'finished') {
                $winner = User::find($auction->winner_user_id);
                // Ensure winningBid relationship is loaded or retrieve explicitly
                // Since winning_bid_id is set, we can eagerly load or access
                $winningBidAmount = $auction->winningBid ? $auction->winningBid->amount_yen : null;
            }

            $this->notifyAdmins($auction, $finalStatus, $winner, $winningBidAmount);
        });
    }

    /**
     * Notify all administrators about the settled auction.
     */
    private function notifyAdmins(Auction $auction, string $status, ?User $winner, ?int $winningBidAmount): void
    {
        $admins = User::where('role', \App\Enums\UserRole::Admin->value)->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new AdminAuctionSettledNotification($auction, $status, $winner, $winningBidAmount));
            } catch (\Exception $e) {
                Log::error("Failed to notify admin {$admin->id} about settled auction {$auction->id}: ".$e->getMessage());
            }
        }
    }
}

