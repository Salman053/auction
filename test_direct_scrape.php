<?php

require_once 'vendor/autoload.php';

use App\Models\Auction;
use App\Services\ScraperService;
use App\Services\YahooAuctionHtmlParser;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════════\n";
echo "  DIRECT SCRAPE TEST\n";
echo "═══════════════════════════════════════════════════════\n\n";

// Create parser and scraper directly
$parser = new YahooAuctionHtmlParser;
$scraper = new ScraperService($parser);

echo "Testing search for 'iPhone'...\n\n";

$results = $scraper->search('iPhone', 1);

echo "\nResults found: ".count($results)."\n";

if (count($results) > 0) {
    echo "\nFirst 5 results:\n";
    echo str_repeat('-', 80)."\n";

    foreach (array_slice($results, 0, 5) as $index => $item) {
        echo ($index + 1).". ID: {$item['yahoo_auction_id']}\n";
        echo "   Title: {$item['title']}\n";
        echo "   Price: ¥{$item['current_bid_yen']}\n";
        echo "   Link: {$item['link']}\n\n";
    }

    // Ask if user wants to save to database
    echo 'Do you want to save these to the database? (y/n): ';
    $handle = fopen('php://stdin', 'r');
    $line = fgets($handle);
    if (trim(strtolower($line)) == 'y') {
        $created = 0;
        foreach ($results as $item) {
            $exists = Auction::where('yahoo_auction_id', $item['yahoo_auction_id'])->exists();
            if (! $exists) {
                Auction::create([
                    'yahoo_auction_id' => $item['yahoo_auction_id'],
                    'title' => $item['title'],
                    'current_bid_yen' => $item['current_bid_yen'],
                    'thumbnail_url' => $item['thumbnail_url'],
                    'status' => 'active',
                    'last_synced_at' => now(),
                ]);
                $created++;
                echo "✓ Saved: {$item['title']}\n";
            }
        }
        echo "\n✅ Saved {$created} new auctions to database!\n";
        echo 'Total auctions in DB: '.Auction::count()."\n";
    }
} else {
    echo "No results found!\n";
    echo "Check if Yahoo is blocking your requests.\n";
}

echo "\n═══════════════════════════════════════════════════════\n";
