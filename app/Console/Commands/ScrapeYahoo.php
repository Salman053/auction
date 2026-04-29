<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\ScrapingLog;
use App\Services\ScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScrapeYahoo extends Command
{
    protected $signature = 'yahoo:scrape 
                            {keyword? : Search keyword} 
                            {--pages=1 : Number of pages to scrape}
                            {--fetch-details : Fetch full auction details including end dates during search}
                            {--force-details : Force re-fetching details even if auction exists}
                            {--update-all : Update existing active auctions}
                            {--delay=2 : Delay between requests in seconds}';

    protected $description = 'Scrape Yahoo Auctions Japan and import/update auction data';

    public function handle(ScraperService $scraper): int
    {
        $keyword = $this->argument('keyword');
        $pages = (int) $this->option('pages');
        $updateAll = $this->option('update-all');
        $delay = (int) $this->option('delay');

        if (! $keyword && ! $updateAll) {
            $this->error('Please provide a keyword or use --update-all flag');

            return self::FAILURE;
        }

        $log = ScrapingLog::create([
            'run_uuid' => (string) Str::uuid(),
            'status' => 'running',
            'started_at' => now(),
            'meta' => [
                'keyword' => $keyword,
                'max_pages' => $pages,
                'update_all' => $updateAll,
            ],
        ]);

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  YAHOO AUCTIONS SCRAPER');
        $this->info("═══════════════════════════════════════════════════════\n");

        try {
            if ($updateAll) {
                $this->updateExistingAuctions($scraper, $log, $delay);
            } elseif ($keyword) {
                $this->searchNewAuctions($scraper, $keyword, $pages, $delay, $log);
            }

            $log->update(['status' => 'completed', 'ended_at' => now()]);
            $this->showSummary($log);
        } catch (\Throwable $e) {
            $this->error('❌ Error: '.$e->getMessage());
            $log->update([
                'status' => 'failed',
                'ended_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function searchNewAuctions(ScraperService $scraper, string $keyword, int $pages, int $delay, ScrapingLog $log): void
    {
        $this->info("🔍 Searching for: \"{$keyword}\" ({$pages} pages)\n");

        $created = 0;
        $updated = 0;
        $allResults = [];
        // boolean options are already true/false, no need for null coalescing
        $fetchDetails = $this->option('fetch-details');
        $forceDetails = $this->option('force-details');

        for ($page = 1; $page <= $pages; $page++) {
            $this->line("📄 Fetching page {$page}...");
            $results = $scraper->search($keyword, $page);

            if (empty($results)) {
                $this->warn("⚠️ No results found on page {$page}. Stopping.");
                break;
            }

            $allResults = array_merge($allResults, $results);

            foreach ($results as $index => $item) {
                $titlePreview = substr($item['title'] ?? '', 0, 50);
                $this->line('   Processing item '.($index + 1).': '.$titlePreview);

                $auction = Auction::where('yahoo_auction_id', $item['yahoo_auction_id'])->first();

                if ($auction) {
                    // Update basic info
                    $auction->update([
                        'title' => $item['title'],
                        'current_bid_yen' => $item['current_bid_yen'],
                        'thumbnail_url' => $item['thumbnail_url'] ?? null,
                        'last_synced_at' => now(),
                    ]);

                    if ($forceDetails) {
                        $this->line('      📅 Force fetching details...');
                        $details = $scraper->getAuctionDetails($item['yahoo_auction_id']);
                        if (! empty($details)) {
                            $auction->update([
                                'ends_at' => $details['ends_at'] ?? $auction->ends_at,
                                'shipping_fee_yen' => $details['shipping_fee_yen'] ?? $auction->shipping_fee_yen,
                                'seller_name' => $details['seller_name'] ?? $auction->seller_name,
                                'yahoo_seller_id' => $details['yahoo_seller_id'] ?? $auction->yahoo_seller_id,
                                'seller_rating' => $details['seller_rating'] ?? $auction->seller_rating,
                                'image_urls' => $details['image_urls'] ?? $auction->image_urls,
                            ]);
                            $this->line("      👤 Seller: {$auction->seller_name} | ⭐: {$auction->seller_rating}");
                        }
                        // FIX: Add delay after forced detail fetch
                        sleep($delay);
                    }

                    $updated++;
                    $this->line('      🔄 Updated existing auction');
                } else {
                    $auctionData = [
                        'yahoo_auction_id' => $item['yahoo_auction_id'],
                        'title' => $item['title'],
                        'current_bid_yen' => $item['current_bid_yen'],
                        'thumbnail_url' => $item['thumbnail_url'] ?? null,
                        'status' => 'active',
                        'last_synced_at' => now(),
                    ];

                    if ($fetchDetails) {
                        $this->line('      📅 Fetching details for end date...');
                        $details = $scraper->getAuctionDetails($item['yahoo_auction_id']);
                        if (isset($details['ends_at'])) {
                            $auctionData['ends_at'] = $details['ends_at'];
                        }
                        if (isset($details['seller_name'])) {
                            $auctionData['seller_name'] = $details['seller_name'];
                        }
                        if (isset($details['yahoo_seller_id'])) {
                            $auctionData['yahoo_seller_id'] = $details['yahoo_seller_id'];
                        }
                        if (isset($details['shipping_fee_yen'])) {
                            $auctionData['shipping_fee_yen'] = $details['shipping_fee_yen'];
                        }
                        if (isset($details['seller_rating'])) {
                            $auctionData['seller_rating'] = $details['seller_rating'];
                        }
                        if (isset($details['image_urls'])) {
                            $auctionData['image_urls'] = $details['image_urls'];
                        }

                        $this->line('      👤 Seller: '.($details['seller_name'] ?? 'N/A'));
                        sleep($delay); // delay after detail fetch
                    }

                    Auction::create($auctionData);
                    $created++;
                    $this->line('      ✅ Created new auction');
                }
            }

            $this->info("✅ Page {$page}: Processed ".count($results).' items');
            $this->info("   📊 Totals - Created: {$created}, Updated: {$updated}\n");

            if ($page < $pages) {
                $this->line("⏳ Waiting {$delay} seconds...\n");
                sleep($delay);
            }
        }

        $log->update([
            'auctions_created' => $created,
            'auctions_updated' => $updated,
            'meta' => array_merge($log->meta ?? [], ['total_found' => count($allResults)]),
        ]);

        $this->info("\n✨ Search completed!");
        $this->info("📊 Created: {$created}, Updated: {$updated}, Total found: ".count($allResults));
    }

    private function updateExistingAuctions(ScraperService $scraper, ScrapingLog $log, int $delay): void
    {
        $this->info("🔄 Updating existing auctions\n");
        $auctions = Auction::where('status', 'active')->get();

        if ($auctions->isEmpty()) {
            $this->warn('No auctions found to update.');

            return;
        }

        $this->info('Found '.$auctions->count()." auctions to update\n");
        $updated = 0;
        $closed = 0;
        $failed = 0;

        foreach ($auctions as $auction) {
            $this->line("Updating: {$auction->yahoo_auction_id} - ".substr($auction->title, 0, 50));

            try {
                $details = $scraper->getAuctionDetails($auction->yahoo_auction_id);

                if (isset($details['error']) && $details['error'] === 'blocked') {
                    $this->error('  ⚠️ Access blocked');
                    $failed++;

                    continue;
                }

                if (empty($details) || empty($details['yahoo_auction_id'])) {
                    $auction->update(['status' => 'closed', 'last_synced_at' => now()]);
                    $closed++;
                    $this->warn('  🚫 Marked as closed');
                } else {
                    $auction->update([
                        'current_bid_yen' => $details['current_bid_yen'] ?? $auction->current_bid_yen,
                        'shipping_fee_yen' => $details['shipping_fee_yen'] ?? $auction->shipping_fee_yen,
                        'ends_at' => $details['ends_at'] ?? $auction->ends_at,
                        'seller_name' => $details['seller_name'] ?? $auction->seller_name,
                        'yahoo_seller_id' => $details['yahoo_seller_id'] ?? $auction->yahoo_seller_id,
                        'seller_rating' => $details['seller_rating'] ?? $auction->seller_rating,
                        'image_urls' => $details['image_urls'] ?? $auction->image_urls,
                        'last_synced_at' => now(),
                    ]);
                    $updated++;
                    $this->info('  ✅ Updated successfully');
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error('  ❌ Failed: '.$e->getMessage());
            }

            if ($delay > 0) {
                sleep($delay);
            }
        }

        $log->update([
            'auctions_updated' => $updated,
            'auctions_closed' => $closed,
            'meta' => array_merge($log->meta ?? [], [
                'updated_count' => $updated,
                'closed_count' => $closed,
                'failed_count' => $failed,
            ]),
        ]);

        $this->info("\n✨ Update completed!");
        $this->info("📊 Updated: {$updated}, Closed: {$closed}, Failed: {$failed}");
    }

    private function showSummary(ScrapingLog $log): void
    {
        // ... (unchanged, but remove 'ended' count or set to 0)
        $this->line("\n");
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  SUMMARY');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info("Run UUID: {$log->run_uuid}");
        $this->info("Status: {$log->status}");
        $this->info("Created: {$log->auctions_created}");
        $this->info("Updated: {$log->auctions_updated}");
        $this->info("Closed: {$log->auctions_closed}");
        $this->info("Started: {$log->started_at}");
        $this->info("Ended: {$log->ended_at}");

        if ($log->started_at && $log->ended_at) {
            $duration = $log->started_at->diffInSeconds($log->ended_at);
            $this->info("Duration: {$duration} seconds");
        }

        $totalActive = Auction::where('status', 'active')->count();
        $totalClosed = Auction::where('status', 'closed')->count();
        // $totalEnded is not used – we only have active/closed states
        $this->info("\n📊 Database Statistics:");
        $this->info("  Active auctions: {$totalActive}");
        $this->info("  Closed auctions: {$totalClosed}");
        $this->info('  Total auctions: '.Auction::count());

        $sample = Auction::latest()->take(5)->get();
        if ($sample->isNotEmpty()) {
            $this->info("\n📋 Latest 5 auctions saved:");
            foreach ($sample as $auction) {
                $this->line("  • {$auction->yahoo_auction_id} - ".substr($auction->title, 0, 50)." - ¥{$auction->current_bid_yen}");
            }
        }
    }
}
