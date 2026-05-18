<?php

namespace App\Jobs;

use App\Models\Auction;
use App\Services\AuctionReconciliationService;
use App\Services\ScraperService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAuctionDetails implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 10, 30];

    public function __construct(public Auction $auction, public bool $force = false) {}

    public function handle(ScraperService $scraper): void
    {
        $this->auction->refresh();

        if (! $this->force) {
            $lastSynced = $this->auction->last_synced_at;
            if ($lastSynced) {
                // If it's closed/ended, don't sync again
                if ($this->auction->status === 'finished' || $this->auction->status === 'closed') {
                    return;
                }

                // If ends in less than 5 minutes, sync if older than 5 seconds
                if ($this->auction->ends_at && $this->auction->ends_at->diffInMinutes(now(), false) < 5) {
                    if ($lastSynced->gt(now()->subSeconds(5))) {
                        return;
                    }
                }
                // If ends in less than 1 hour, sync if older than 15 seconds
                elseif ($this->auction->ends_at && $this->auction->ends_at->diffInMinutes(now(), false) < 60) {
                    if ($lastSynced->gt(now()->subSeconds(15))) {
                        return;
                    }
                }
                // If ends in less than 24 hours, sync if older than 2 minutes
                elseif ($this->auction->ends_at && $this->auction->ends_at->diffInHours(now(), false) < 24) {
                    if ($lastSynced->gt(now()->subMinutes(2))) {
                        return;
                    }
                }
                // Otherwise, sync if older than 10 minutes
                else {
                    if ($lastSynced->gt(now()->subMinutes(10))) {
                        return;
                    }
                }
            }
        }

        $details = $scraper->getAuctionDetails($this->auction->yahoo_auction_id);

        if (! empty($details)) {
            $this->auction->update([
                'title' => $details['title'] ?? $this->auction->title,
                'current_bid_yen' => $details['current_bid_yen'] ?? $this->auction->current_bid_yen,
                'starting_bid_yen' => $details['starting_bid_yen'] ?? $this->auction->starting_bid_yen,
                'bid_count' => $details['bid_count'] ?? $this->auction->bid_count,
                'starts_at' => $details['starts_at'] ?? $this->auction->starts_at,
                'auto_extension' => $details['auto_extension'] ?? $this->auction->auto_extension,
                'ends_at' => $details['ends_at'] ?? $this->auction->ends_at,
                'status' => $details['status'] ?? $this->auction->status,
                'seller_name' => $details['seller_name'] ?? $this->auction->seller_name,
                'yahoo_seller_id' => $details['yahoo_seller_id'] ?? $this->auction->yahoo_seller_id,
                'seller_rating' => $details['seller_rating'] ?? $this->auction->seller_rating,
                'image_urls' => $details['image_urls'] ?? $this->auction->image_urls,
                'yahoo_watcher_count' => $details['watcher_count'] ?? $this->auction->yahoo_watcher_count,
                'last_synced_at' => now(),
            ]);

            // Reconcile internal bids with Yahoo's new price
            app(AuctionReconciliationService::class)->reconcile($this->auction);
        }
    }
}
