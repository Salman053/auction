<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Services\YahooAuctionHtmlParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportYahooHtml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:import-yahoo-html {path : File or directory path (HTML)} {--url= : Optional source URL for ID extraction}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Yahoo auction HTML saved locally (offline parser; no HTTP requests).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = (string) $this->argument('path');

        $url = $this->option('url');
        $url = is_string($url) && $url !== '' ? $url : null;

        if (! File::exists($path)) {
            $this->warn("Path not found (skipped): {$path}");

            return self::SUCCESS;
        }

        $files = [];

        if (File::isDirectory($path)) {
            $files = collect(File::files($path))
                ->filter(fn ($file) => in_array(strtolower((string) $file->getExtension()), ['html', 'htm'], true))
                ->map(fn ($file) => $file->getPathname())
                ->values()
                ->all();
        } else {
            $files = [$path];
        }

        if ($files === []) {
            $this->warn('No HTML files found.');

            return self::SUCCESS;
        }

        $parser = app(YahooAuctionHtmlParser::class);
        $imported = 0;

        foreach ($files as $filePath) {
            $html = File::get($filePath);
            $parsed = $parser->parseAuctionDetailHtml($html, $url);

            $yahooId = $parsed['yahoo_auction_id'];
            if (! is_string($yahooId) || $yahooId === '') {
                $this->warn("Skipped (no yahoo_auction_id): {$filePath}");

                continue;
            }

            Auction::query()->updateOrCreate(
                ['yahoo_auction_id' => $yahooId],
                [
                    'title' => (string) ($parsed['title'] ?? ''),
                    'current_bid_yen' => (int) ($parsed['current_bid_yen'] ?? 0),
                    'starting_bid_yen' => 0,
                    'bid_count' => 0,
                    'status' => 'active',
                    'ends_at' => $parsed['ends_at'],
                    'seller_name' => $parsed['seller_name'],
                    'seller_rating' => $parsed['seller_rating'],
                    'thumbnail_url' => $parsed['thumbnail_url'],
                    'image_urls' => $parsed['image_urls'],
                    'raw' => $parsed['raw'],
                    'last_synced_at' => now(),
                ],
            );

            $this->line("Imported: {$yahooId} ({$filePath})");
            $imported++;
        }

        $this->info("Done. Imported: {$imported}");

        return self::SUCCESS;
    }
}
