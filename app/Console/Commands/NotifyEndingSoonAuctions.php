<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\User;
use App\Notifications\AuctionEndingSoonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class NotifyEndingSoonAuctions extends Command
{
    protected $signature = 'auctions:notify-ending-soon {minutes=60}';
    protected $description = 'Notify users about auctions ending within the specified number of minutes';

    public function handle()
    {
        $minutes = (int) $this->argument('minutes');
        $endTime = now()->addMinutes($minutes);

        $auctions = Auction::query()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', $endTime)
            ->get();

        if ($auctions->isEmpty()) {
            $this->info("No auctions ending in the next {$minutes} minutes.");
            return 0;
        }

        foreach ($auctions as $auction) {
            $this->info("Processing auction: {$auction->title} (Ends at: {$auction->ends_at})");

            // Get bidders
            $bidders = User::whereHas('bids', function($q) use ($auction) {
                $q->where('auction_id', $auction->id)->where('status', 'active');
            })->get();

            // Get watchers
            $watchers = User::whereHas('watchlistItems', function($q) use ($auction) {
                $q->where('auction_id', $auction->id);
            })->get();

            $usersToNotify = $bidders->concat($watchers)->unique('id');

            foreach ($usersToNotify as $user) {
                // Prevent duplicate notifications for the same auction within a window
                $cacheKey = "notified_ending_soon_{$user->id}_{$auction->id}";
                if (!Cache::has($cacheKey)) {
                    $user->notify(new AuctionEndingSoonNotification($auction));
                    Cache::put($cacheKey, true, now()->addHours(24));
                    $this->line(" - Notified user: {$user->email}");
                }
            }
        }

        $this->info("Notification process complete.");
        return 0;
    }
}
