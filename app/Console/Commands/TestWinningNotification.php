<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\AuctionWonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestWinningNotification extends Command
{
    protected $signature = 'test:send-won-email {email=salmankhanm859@gmail.com}';
    protected $description = 'Send a test Auction Won notification to a specific email';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->info("User not found, creating test user...");
            $user = User::create([
                'name' => 'Salman Khan (Test)',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance_yen' => 1000000,
                'locked_balance_yen' => 0,
            ]);
        }

        $auction = Auction::first();
        if (!$auction) {
            $this->info("Creating a dummy auction for testing...");
            $auction = Auction::create([
                'yahoo_auction_id' => 't123456789',
                'title' => 'Luxury Rolex Cosmograph Daytona (Test)',
                'current_bid_yen' => 3000,
                'status' => 'finished',
                'ends_at' => now()->subDay(),
                'winner_user_id' => $user->id,
            ]);
        } else {
            $auction->update([
                'status' => 'finished',
                'winner_user_id' => $user->id,
                'current_bid_yen' => 3500000,
            ]);
        }

        $this->info("Sending notification to {$email}...");
        
        $user->notify(new AuctionWonNotification($auction, 3500000));

        $this->info("Notification sent! Check your email (or logs if using log driver).");
        
        return 0;
    }
}
