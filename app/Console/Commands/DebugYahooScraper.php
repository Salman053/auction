<?php

namespace App\Console\Commands;

use App\Services\YahooAuctionHtmlParser;
use Illuminate\Console\Command;

class DebugYahooScraper extends Command
{
    protected $signature = 'yahoo:debug {keyword=iPhone}';

    protected $description = 'Debug Yahoo Auctions scraping';

    public function handle(YahooAuctionHtmlParser $parser)
    {
        $keyword = $this->argument('keyword');

        $this->info("Debugging Yahoo Auctions for: {$keyword}\n");

        // Fetch the page
        $url = 'https://auctions.yahoo.co.jp/search/search?p='.urlencode($keyword).'&b=1';
        $this->info("URL: {$url}\n");

        $verifySsl = (bool) env('SCRAPER_VERIFY_SSL', true);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_TIMEOUT => 30,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info("HTTP Status: {$httpCode}");
        $this->info('HTML Size: '.strlen($html)." bytes\n");

        // Save HTML for inspection
        $debugFile = storage_path("debug_yahoo_{$keyword}.html");
        file_put_contents($debugFile, $html);
        $this->info("HTML saved to: {$debugFile}\n");

        // Check if blocked
        if (str_contains($html, 'アクセスが制限') || str_contains($html, 'Access restricted')) {
            $this->error('⚠️ ACCESS BLOCKED BY YAHOO!');
            $this->error('Yahoo Japan is blocking your requests.');

            return 1;
        }

        // Try to find auction items
        $this->info("Searching for auction items in HTML...\n");

        $patterns = [
            'Product__titleLink',
            'Product__priceValue',
            'data-auction-id',
            'auction/',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($html, $pattern)) {
                $this->info("✅ Found pattern: {$pattern}");
            } else {
                $this->warn("❌ Missing pattern: {$pattern}");
            }
        }

        // Try to parse with your parser
        $this->info("\nAttempting to parse with YahooAuctionHtmlParser...");
        $results = $parser->parseSearchResults($html);

        $this->info('Parser found: '.count($results).' results');

        if (count($results) > 0) {
            $this->info("\nFirst result:");
            print_r($results[0]);
        }

        return 0;
    }
}
