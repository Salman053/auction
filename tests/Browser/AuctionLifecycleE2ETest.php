<?php

namespace Tests\Browser;

use App\Models\Auction;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuctionLifecycleE2ETest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test the complete end-to-end flow from deposit to shipment approval.
     */
    public function test_full_auction_lifecycle()
    {
        // -----------------------------------------------------------------
        // Set up test data
        // -----------------------------------------------------------------
        $user = User::factory()->create([
            'email' => 'test_bidder@example.com',
            'name' => 'Test Bidder',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $user->wallet()->create(['balance_yen' => 0]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $shippingRate = ShippingRate::create([
            'name' => 'Japan Priority',
            'fee_yen' => 1000,
            'country' => 'Japan',
        ]);
        $user->update(['shipping_rate_id' => $shippingRate->id]);

        $auction = Auction::factory()->create([
            'id' => 187,
            'yahoo_auction_id' => 'test_187',
            'title' => 'Vintage Rolex Submariner',
            'starting_bid_yen' => 1000,
            'current_bid_yen' => 1000,
            'bid_count' => 0,
            'status' => 'active',
            'ends_at' => now()->addMinutes(60),
            'last_synced_at' => now(),
            'image_urls' => [
                'https://placehold.co/800x600?text=Rolex+1',
                'https://placehold.co/800x600?text=Rolex+2',
                'https://placehold.co/800x600?text=Rolex+3',
                'https://placehold.co/800x600?text=Rolex+4',
            ],
        ]);

        $this->browse(function (Browser $userBrowser, Browser $adminBrowser) use ($user, $admin, $auction, $shippingRate) {
            
            // 1. Deposit (User)
            $userBrowser->visit('/login')
                    ->type('email', 'test_bidder@example.com')
                    ->type('password', 'password')
                    ->press('Authorize Identity')
                    ->waitForLocation('/dashboard')
                    ->visit('/app/wallet')
                    ->pause(2000)
                    ->assertSee('CASH BALANCE')
                    ->type('amount_yen', '100000')
                    ->press('Process Auto-Deposit')
                    ->waitForText('Confirm Deposit Request')
                    ->pause(500)
                    ->click('#confirm-dialog-confirm')
                    ->pause(2000)
                    ->assertSee('Deposit request submitted. An admin must approve it.');

            // 2. Approve Deposit (Admin)
            $adminBrowser->visit('/admin/login')
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Authorize Identity')
                    ->waitForLocation('/admin')
                    ->visit('/admin/deposits')
                    ->pause(2000)
                    ->assertSee('test_bidder@example.com')
                    ->press('Approve')
                    ->waitForText('Approve Deposit')
                    ->pause(500)
                    ->click('#confirm-dialog-confirm')
                    ->pause(2000)
                    ->waitForText('Deposit status updated successfully.');

            // 3. Place a Bid (User)
            $userBrowser->visit('/app/auctions/187')
                    ->pause(2000)
                    ->assertSee('Vintage Rolex Submariner')
                    ->assertVisible('#carousel-container')
                    ->assertSee('CURRENT PRICE')
                    ->type('amount_yen', '5000')
                    ->click('button[data-confirm-title="Confirm Bid Placement"]')
                    ->waitForText('Confirm Bid Placement')
                    ->pause(500)
                    ->click('#confirm-dialog-confirm')
                    ->pause(2000)
                    ->waitForText('Bid Successfully Processed');

            // 4. Win the Auction
            $auction->refresh();
            $auction->update([
                'status' => 'active',
                'ends_at' => now()->subMinute()
            ]);
            Artisan::call(\App\Console\Commands\CloseEndedAuctions::class);

            $auction->refresh();
            $this->assertEquals('finished', $auction->status);
            $this->assertEquals($user->id, $auction->winner_user_id);

            $userBrowser->refresh()
                    ->pause(2000)
                    ->assertSee('CONGRATULATIONS, YOU WON!');

            // 5. Confirm Shipment (User)
            $userBrowser->press('CONFIRM SHIPMENT DETAILS')
                    ->pause(2000)
                    ->assertSee('Awaiting Admin Approval');

            // 6. Final Approval (Admin)
            $adminBrowser->visit('/admin/auctions/187')
                    ->pause(2000)
                    ->assertSee('Vintage Rolex Submariner')
                    ->press('APPROVE SHIPMENT')
                    ->pause(2000)
                    ->waitForText('Shipment approved successfully!');
        });
    }
}
