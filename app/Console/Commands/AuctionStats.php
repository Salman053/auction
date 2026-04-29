<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\ScrapingLog;
use Illuminate\Console\Command;

class AuctionStats extends Command
{
    protected $signature = 'auction:stats';

    protected $description = 'Show auction statistics';

    public function handle()
    {
        $this->table(
            ['Status', 'Count', 'Average Price', 'Total Bids'],
            [
                ['Active', Auction::where('status', 'active')->count(),
                    '¥'.number_format(Auction::where('status', 'active')->avg('current_bid_yen') ?? 0),
                    Auction::where('status', 'active')->sum('bid_count')],

                ['Ended', Auction::where('status', 'ended')->count(),
                    '¥'.number_format(Auction::where('status', 'ended')->avg('current_bid_yen') ?? 0),
                    Auction::where('status', 'ended')->sum('bid_count')],

                ['Closed', Auction::where('status', 'closed')->count(),
                    '¥'.number_format(Auction::where('status', 'closed')->avg('current_bid_yen') ?? 0),
                    Auction::where('status', 'closed')->sum('bid_count')],
            ]
        );

        $lastRun = ScrapingLog::where('status', 'completed')->latest()->first();
        if ($lastRun) {
            $this->info("\nLast scrape run: {$lastRun->created_at->diffForHumans()}");
            $this->info("Created: {$lastRun->auctions_created}, Updated: {$lastRun->auctions_updated}");
        }
    }
}
