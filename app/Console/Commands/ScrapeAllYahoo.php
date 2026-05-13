<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\ScrapingLog;
use App\Services\AuctionReconciliationService;
use App\Services\ScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScrapeAllYahoo extends Command
{
    protected $signature = 'yahoo:scrape-all 
                            {--pages=1 : Number of pages to scrape per category}
                            {--delay=2 : Delay between requests in seconds}
                            {--min= : Minimum price in Yen}
                            {--max= : Maximum price in Yen}
                            {--fetch-details : Fetch full auction details during search}
                            {--force-details : Force re-fetching details even if auction exists}';

    protected $description = 'Scrape all fresh listings from all main categories on Yahoo Auctions Japan';

    public function handle(ScraperService $scraper): int
    {
        $pages = (int) $this->option('pages');
        $delay = (int) $this->option('delay');
        $minPrice = $this->option('min') !== null ? (int) $this->option('min') : null;
        $maxPrice = $this->option('max') !== null ? (int) $this->option('max') : null;
        $fetchDetails = (bool) $this->option('fetch-details');
        $forceDetails = (bool) $this->option('force-details');

        $log = ScrapingLog::create([
            'run_uuid' => (string) Str::uuid(),
            'status' => 'running',
            'started_at' => now(),
            'meta' => [
                'type' => 'all_categories',
                'max_pages' => $pages,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'fetch_details' => $fetchDetails,
                'force_details' => $forceDetails,
            ],
        ]);

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  YAHOO AUCTIONS COMPREHENSIVE SCRAPER');
        $this->info("═══════════════════════════════════════════════════════\n");

        $topLevelCategories = [
            '26318' => '自動車、オートバイ',
            '23000' => 'ファッション、ブランド',
            '23140' => 'アクセサリー、時計',
            '24698' => 'スポーツ、レジャー',
            '23632' => '家電、AV、カメラ',
            '23336' => 'コンピュータ',
            '25464' => 'おもちゃ、ゲーム',
            '24242' => 'ホビー、カルチャー',
            '20000' => 'アンティーク、コレクション',
            '21600' => '本、雑誌、漫画',
            '22152' => '音楽、CD',
            '21964' => '映画、ビデオ、DVD',
            '20060' => 'コミック、アニメグッズ',
            '2084032594' => 'タレントグッズ',
            '24198' => '住まい、インテリア、DIY',
            '22896' => '事務、店舗用品',
            '26086' => '花、園芸、農業',
            '42177' => 'ビューティー、ヘルスケア',
            '24202' => 'ベビー用品',
            '23976' => '食品、飲料',
            '2084055844' => 'ペット、生き物',
            '2084043920' => 'チケット、金券、宿泊予約',
            '2084217893' => 'チャリティー',
            '2084060731' => '不動産',
            '26084' => 'スキル、レンタル、その他',
        ];

        $totalCreated = 0;
        $totalUpdated = 0;

        try {
            foreach ($topLevelCategories as $id => $name) {
                $this->info("📁 Scraping Category: {$name} ({$id})");

                for ($page = 1; $page <= $pages; $page++) {
                    $this->line("   📄 Page {$page}...");
                    $results = $scraper->search('', $page, $id, true, $minPrice, $maxPrice);

                    if (empty($results)) {
                        $this->warn('   ⚠️ No results found. Skipping.');
                        break;
                    }

                    foreach ($results as $item) {
                        $auction = Auction::where('yahoo_auction_id', $item['yahoo_auction_id'])->first();

                        if ($auction) {
                            $auction->update([
                                'yahoo_category_id' => $id,
                                'title' => $item['title'],
                                'current_bid_yen' => $item['current_bid_yen'],
                                'ends_at' => $item['ends_at'] ?? $auction->ends_at,
                                'thumbnail_url' => $item['thumbnail_url'] ?? $auction->thumbnail_url,
                                'last_synced_at' => now(),
                            ]);

                            // Reconcile internal bids
                            app(AuctionReconciliationService::class)->reconcile($auction);

                            if ($forceDetails) {
                                $this->line("      📅 Force fetching details for {$item['yahoo_auction_id']}...");
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
                                }
                                sleep($delay);
                            }

                            $totalUpdated++;
                        } else {
                            $auctionData = [
                                'yahoo_auction_id' => $item['yahoo_auction_id'],
                                'yahoo_category_id' => $id,
                                'title' => $item['title'],
                                'current_bid_yen' => $item['current_bid_yen'],
                                'ends_at' => $item['ends_at'] ?? null,
                                'thumbnail_url' => $item['thumbnail_url'] ?? null,
                                'status' => 'active',
                                'last_synced_at' => now(),
                            ];

                            if ($fetchDetails) {
                                $this->line("      📅 Fetching details for {$item['yahoo_auction_id']}...");
                                $details = $scraper->getAuctionDetails($item['yahoo_auction_id']);
                                if (! empty($details)) {
                                    $auctionData = array_merge($auctionData, [
                                        'ends_at' => $details['ends_at'] ?? $auctionData['ends_at'],
                                        'seller_name' => $details['seller_name'] ?? null,
                                        'yahoo_seller_id' => $details['yahoo_seller_id'] ?? null,
                                        'shipping_fee_yen' => $details['shipping_fee_yen'] ?? null,
                                        'seller_rating' => $details['seller_rating'] ?? null,
                                        'image_urls' => $details['image_urls'] ?? null,
                                    ]);
                                }
                                sleep($delay);
                            }

                            Auction::create($auctionData);
                            $totalCreated++;
                        }
                    }

                    $this->info('   ✅ Processed '.count($results).' items.');

                    if ($page < $pages) {
                        sleep($delay);
                    }
                }

                $this->line('');
                sleep($delay);
            }

            $log->update([
                'status' => 'completed',
                'ended_at' => now(),
                'auctions_created' => $totalCreated,
                'auctions_updated' => $totalUpdated,
            ]);

            $this->info('✨ Comprehensive scrape completed!');
            $this->info("📊 Total Created: {$totalCreated}");
            $this->info("📊 Total Updated: {$totalUpdated}");

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
}
