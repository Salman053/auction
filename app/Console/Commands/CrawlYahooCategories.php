<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\ScraperService;
use App\Services\YahooAuctionHtmlParser;
use Illuminate\Console\Command;

class CrawlYahooCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yahoo:crawl-categories 
                            {--depth=3 : Max depth to crawl} 
                            {--parent= : Yahoo Category ID to start from}
                            {--delay=1 : Delay between requests in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Yahoo Auction category tree and populate categories table';

    /**
     * Execute the console command.
     */
    public function handle(ScraperService $scraper, YahooAuctionHtmlParser $parser): int
    {
        $maxDepth = (int) $this->option('depth');
        $startYahooId = $this->option('parent');
        $delay = (int) $this->option('delay');

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  YAHOO CATEGORY TREE CRAWLER');
        $this->info("═══════════════════════════════════════════════════════\n");

        if ($startYahooId) {
            $parentCategory = Category::where('yahoo_category_id', $startYahooId)->first();
            $this->crawl($scraper, $parser, $startYahooId, $parentCategory?->id, $parentCategory?->depth ?? 0, $maxDepth, $delay);
        } else {
            $this->info('Starting from top-level categories...');
            
            // These are the main top-level categories of Yahoo Auctions Japan
            $topLevelCategories = [
                '23336' => 'コンピュータ',
                '23632' => '家電、AV、カメラ',
                '22152' => '音楽',
                '21600' => '本、雑誌',
                '21964' => '映画、ビデオ',
                '25464' => 'おもちゃ、ゲーム',
                '24242' => 'ホビー、カルチャー',
                '20000' => 'アンティーク、コレクション',
                '24698' => 'スポーツ、レジャー',
                '26318' => '自動車、オートバイ',
                '23000' => 'ファッション',
                '23140' => 'アクセサリー、時計',
                '42177' => 'ビューティー、ヘルスケア',
                '23976' => '食品、飲料',
                '24198' => '住まい、インテリア',
                '2084055844' => 'ペット、生き物',
                '22896' => '事務、店舗用品',
                '26086' => '花、園芸',
                '2084043920' => 'チケット、金券、宿泊予約',
                '24202' => 'ベビー用品',
                '2084032594' => 'タレントグッズ',
                '20060' => 'コミック、アニメグッズ',
                '2084217893' => 'チャリティー',
                '26084' => 'その他',
            ];

            foreach ($topLevelCategories as $id => $name) {
                $category = Category::updateOrCreate(
                    ['yahoo_category_id' => $id],
                    ['name' => $name, 'depth' => 0]
                );
                $this->crawl($scraper, $parser, $id, $category->id, 0, $maxDepth, $delay);
            }
        }

        $this->info("\n✨ Crawl completed!");
        $this->info('Total categories in database: ' . Category::count());

        return self::SUCCESS;
    }

    /**
     * Recursively crawl categories.
     */
    private function crawl(ScraperService $scraper, YahooAuctionHtmlParser $parser, string $yahooId, ?int $parentId, int $currentDepth, int $maxDepth, int $delay): void
    {
        if ($currentDepth >= $maxDepth) {
            return;
        }

        $this->line(str_repeat('  ', $currentDepth) . "🔍 Category: {$yahooId}");

        $url = "https://auctions.yahoo.co.jp/category/list/{$yahooId}/";
        $html = $scraper->getHtml($url);

        if (!$html) {
            $this->error(str_repeat('  ', $currentDepth) . "❌ Failed to fetch: {$yahooId}");
            return;
        }

        $subCategories = $parser->parseCategoryList($html);

        if (empty($subCategories)) {
            $this->line(str_repeat('  ', $currentDepth + 1) . "🍃 Leaf node");
            Category::where('id', $parentId)->update(['is_leaf' => true]);
            return;
        }

        $this->line(str_repeat('  ', $currentDepth + 1) . "🌿 Found " . count($subCategories) . " sub-categories");

        foreach ($subCategories as $sub) {
            // Avoid re-crawling top-level if they appear (sometimes they do in footers)
            if ($currentDepth > 0) {
                 // Check if it's a known top-level ID to avoid loops
                 $topLevelIds = [
                    '23336', '23632', '22152', '21600', '21964', '25464', '24242', '20000', 
                    '24698', '26318', '23000', '23140', '42177', '23976', '24198', 
                    '2084055844', '22896', '26086', '2084043920', '24202', '2084032594', 
                    '20060', '2084217893', '26084'
                 ];
                 if (in_array($sub['yahoo_category_id'], $topLevelIds)) {
                     continue;
                 }
            }

            $cat = Category::updateOrCreate(
                ['yahoo_category_id' => $sub['yahoo_category_id']],
                [
                    'name' => $sub['name'],
                    'parent_id' => $parentId,
                    'depth' => $currentDepth + 1,
                ]
            );

            // Small delay between requests to be polite
            if ($delay > 0) {
                sleep($delay);
            }

            $this->crawl($scraper, $parser, $sub['yahoo_category_id'], $cat->id, $currentDepth + 1, $maxDepth, $delay);
        }
    }
}
