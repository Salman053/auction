<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Jobs\SyncAuctionDetails;
use App\Models\Auction;
use App\Models\ScrapingLog;
use App\Models\User;
use App\Notifications\ScraperCompleted;
use App\Services\AuctionReconciliationService;
use App\Services\ScraperService;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
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
        // Prevent overlapping runs
        if (Cache::has('scraper:running')) {
            $this->warn('⚠️ Scraper is already running. Skipping.');

            return self::FAILURE;
        }

        Cache::put('scraper:running', true, now()->addMinutes(60));

        try {
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

            foreach ($topLevelCategories as $id => $name) {
                if (Cache::has('scraper:stop_requested')) {
                    $this->warn('🛑 Stop requested by admin. Halting.');
                    break;
                }

                $this->info("📁 Scraping Category: {$name} ({$id})");

                for ($page = 1; $page <= $pages; $page++) {
                    if (Cache::has('scraper:stop_requested')) {
                        $this->warn('🛑 Stop requested. Halting after this category.');
                        break 2;
                    }

                    $this->line("   📄 Page {$page}...");
                    $results = $scraper->search('', $page, $id, true, $minPrice, $maxPrice);

                    if (empty($results)) {
                        $this->warn('   ⚠️ No results found. Skipping.');
                        break;
                    }

                    $yids = collect($results)->pluck('yahoo_auction_id')->filter()->toArray();
                    $existingAuctions = Auction::withTrashed()
                        ->whereIn('yahoo_auction_id', $yids)
                        ->get()
                        ->keyBy('yahoo_auction_id');

                    foreach ($results as $item) {
                        $yid = trim((string) $item['yahoo_auction_id']);
                        if (empty($yid)) {
                            continue;
                        }

                        $auction = $existingAuctions->get($yid);

                        if ($auction) {
                            if ($auction->trashed()) {
                                $auction->restore();
                            }
                            $auction->update([
                                'yahoo_category_id' => $id,
                                'title' => $item['title'],
                                'current_bid_yen' => $item['current_bid_yen'],
                                'ends_at' => $item['ends_at'] ?? $auction->ends_at,
                                'thumbnail_url' => $item['thumbnail_url'] ?? $auction->thumbnail_url,
                                'status' => $item['status'] ?? 'active',
                                'last_synced_at' => now(),
                            ]);

                            app(AuctionReconciliationService::class)->reconcile($auction);

                            if ($forceDetails) {
                                SyncAuctionDetails::dispatch($auction)->onQueue('low');
                            }

                            $totalUpdated++;
                        } else {
                            $auctionData = [
                                'yahoo_auction_id' => $yid,
                                'yahoo_category_id' => $id,
                                'title' => $item['title'],
                                'current_bid_yen' => $item['current_bid_yen'],
                                'ends_at' => $item['ends_at'] ?? null,
                                'thumbnail_url' => $item['thumbnail_url'] ?? null,
                                'status' => $item['status'] ?? 'active',
                                'last_synced_at' => now(),
                            ];

                            try {
                                $newAuction = Auction::create($auctionData);
                                $totalCreated++;

                                if ($fetchDetails) {
                                    SyncAuctionDetails::dispatch($newAuction)->onQueue('low');
                                }
                            } catch (QueryException $e) {
                                if ($e->getCode() == 23000) {
                                    // Race condition fallback
                                    $retryAuction = Auction::withTrashed()->where('yahoo_auction_id', $yid)->first();
                                    if ($retryAuction) {
                                        if ($retryAuction->trashed()) {
                                            $retryAuction->restore();
                                        }
                                        $retryAuction->update($auctionData);
                                        $totalUpdated++;

                                        if ($forceDetails) {
                                            SyncAuctionDetails::dispatch($retryAuction)->onQueue('low');
                                        }
                                    }
                                } else {
                                    throw $e;
                                }
                            }
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

            if ($totalCreated > 0) {
                $duration = now()->diffInSeconds($log->started_at);
                $admins = User::where('role', UserRole::Admin->value)->get();
                if ($admins->isNotEmpty()) {
                    Notification::send(
                        $admins,
                        new ScraperCompleted($totalCreated, $totalUpdated, $duration)
                    );
                }
            }

        } catch (\Throwable $e) {
            $this->error('❌ Error: '.$e->getMessage());
            if (isset($log)) {
                $log->update([
                    'status' => 'failed',
                    'ended_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }

            return self::FAILURE;
        } finally {
            Cache::forget('scraper:running');
        }

        return self::SUCCESS;
    }
}
