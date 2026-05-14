<?php

namespace App\Jobs;

use App\Models\Auction;
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

    public function __construct(public Auction $auction)
    {
        $this->onQueue('sync');
    }

    public function handle(ScraperService $scraper): void
    {
        $this->auction->refresh();

        // Skip if recently synced AND we already have image details, to avoid hammering the API with duplicates
        if (!empty($this->auction->image_urls) && $this->auction->last_synced_at && $this->auction->last_synced_at->gt(now()->subHour())) {
            return;
        }

        $details = $scraper->getAuctionDetails($this->auction->yahoo_auction_id);

        if (! empty($details)) {
            $this->auction->update([
                'ends_at' => $details['ends_at'] ?? $this->auction->ends_at,
                'status' => $details['status'] ?? $this->auction->status,
                'shipping_fee_yen' => $details['shipping_fee_yen'] ?? $this->auction->shipping_fee_yen,
                'seller_name' => $details['seller_name'] ?? $this->auction->seller_name,
                'yahoo_seller_id' => $details['yahoo_seller_id'] ?? $this->auction->yahoo_seller_id,
                'seller_rating' => $details['seller_rating'] ?? $this->auction->seller_rating,
                'image_urls' => $details['image_urls'] ?? $this->auction->image_urls,
                'yahoo_watcher_count' => $details['watcher_count'] ?? $this->auction->yahoo_watcher_count,
                'last_synced_at' => now(),
            ]);
        }
    }
}
