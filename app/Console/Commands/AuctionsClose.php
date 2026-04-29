<?php

namespace App\Console\Commands;

use App\Models\Auction;
use Illuminate\Console\Command;

class AuctionsClose extends Command
{
    protected $signature = 'auctions:close 
                            {--dry-run : Simulate without actually updating}
                            {--force : Force close auctions older than 30 days even without end date}';

    protected $description = 'Close auctions that have ended based on their end date';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  CLOSING ENDED AUCTIONS');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $now = now();
        $this->info("Current time: {$now->format('Y-m-d H:i:s')}");
        $this->newLine();

        // Close auctions whose end date has passed
        $endedAuctions = Auction::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->get();

        $closedCount = 0;

        if ($endedAuctions->isNotEmpty()) {
            $this->info("Found {$endedAuctions->count()} auction(s) with passed end dates:\n");
            foreach ($endedAuctions as $auction) {
                $this->line("  • {$auction->yahoo_auction_id} - ".substr($auction->title, 0, 50));
                $this->line("    Ended at: {$auction->ends_at->format('Y-m-d H:i:s')}");
                $this->line('    Ended '.$auction->ends_at->diffForHumans());

                if (! $dryRun) {
                    $auction->update(['status' => 'closed', 'last_synced_at' => $now]);
                    $this->line('    ✅ CLOSED');
                } else {
                    $this->line('    🔄 Would close (dry-run)');
                }
                $closedCount++;
                $this->newLine();
            }
        } else {
            $this->info('No auctions with passed end dates found.');
            $this->newLine();
        }

        // Force close old auctions without end dates (fixed mutation)
        if ($force) {
            $oldAuctions = Auction::where('status', 'active')
                ->whereNull('ends_at')
                ->where('created_at', '<', now()->subDays(30))  // FIXED: no mutation of $now
                ->get();

            if ($oldAuctions->isNotEmpty()) {
                $this->warn("Found {$oldAuctions->count()} old auction(s) without end dates:\n");
                foreach ($oldAuctions as $auction) {
                    $this->line("  • {$auction->yahoo_auction_id} - ".substr($auction->title, 0, 50));
                    $this->line("    Created: {$auction->created_at->diffForHumans()}");

                    if (! $dryRun) {
                        $auction->update(['status' => 'closed', 'last_synced_at' => $now]);
                        $this->line('    ✅ CLOSED (forced)');
                    } else {
                        $this->line('    🔄 Would close (dry-run, forced)');
                    }
                    $closedCount++;
                    $this->newLine();
                }
            }
        }

        // Summary (removed non-existent 'ended' status)
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  SUMMARY');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info("Total auctions closed: {$closedCount}");

        $active = Auction::where('status', 'active')->count();
        $closed = Auction::where('status', 'closed')->count();
        $withEndDates = Auction::whereNotNull('ends_at')->count();
        $withoutEndDates = Auction::whereNull('ends_at')->count();

        $this->newLine();
        $this->info('📊 Current database stats:');
        $this->info("  Active auctions: {$active}");
        $this->info("  Closed auctions: {$closed}");
        $this->info("  Auctions with end dates: {$withEndDates}");
        $this->info("  Auctions without end dates: {$withoutEndDates}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  This was a DRY RUN. Run without --dry-run to apply changes.');
        }

        return self::SUCCESS;
    }
}
