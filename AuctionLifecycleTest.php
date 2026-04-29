<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Auction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuctionLifecycleTest extends DuskTestCase
{
    /**
     * Test the complete end-to-end flow from deposit to shipment approval.
     */
    public function test_complete_auction_bid_to_shipment_lifecycle(): void
    {
        $this->browse(function (Browser $user, Browser $admin) {
            // 1. Deposit (User)
            $user->visit('/login')
                ->type('email', 'test_bidder@example.com')
                ->type('password', 'password')
                ->press('Log in')
                ->visit('/app/wallet')
                ->type('amount_yen', '100000')
                ->press('Request Deposit')
                ->assertSee('Deposit request submitted')
                ->click('#user-menu-button') // Assuming standard Tailwind UI logout
                ->clickLink('Log Out');

            // 2. Approve Deposit (Admin)
            $admin->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Log in')
                ->visit('/admin/wallet/deposits')
                ->with('.deposit-requests-table', function ($table) {
                    $table->assertSee('test_bidder@example.com')
                          ->press('Approve');
                })
                ->assertSee('Deposit approved')
                ->click('#admin-menu-button')
                ->clickLink('Log Out');

            // 3. Place a Bid (User)
            $user->visit('/login')
                ->type('email', 'test_bidder@example.com')
                ->type('password', 'password')
                ->press('Log in')
                ->visit('/app/auctions/187')
                
                // Visual Verification: Image Carousel & New UI
                ->assertVisible('.image-carousel')
                ->assertSee('Current Price')
                ->assertSee('Highest Max Bid')
                
                // Placing the proxy bid
                ->type('max_amount_yen', '5000')
                ->press('Place Bid')
                ->assertSee('Bid placed successfully');

            // 4. Win the Auction
            // We simulate the cron/command execution
            $this->artisan('auctions:close');

            $user->refresh()
                ->assertPathIs('/app/auctions/187')
                ->assertSee('Congratulations, You Won!')
                ->assertSee('Status: Won');

            // 5. Confirm Shipment (User)
            $user->press('Confirm Shipment Details')
                ->waitForText('Awaiting Admin Approval')
                ->assertSee('Awaiting Admin Approval')
                ->click('#user-menu-button')
                ->clickLink('Log Out');

            // 6. Final Approval (Admin)
            $admin->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Log in')
                ->visit('/admin/dashboard')
                ->clickLink('Auctions')
                ->clickLink('Shipment Pending')
                
                // Find the auction record in the pending shipment tab
                ->with('.shipment-pending-table', function ($table) {
                    $table->assertSee('Auction #187')
                          ->press('Approve Shipment');
                })
                ->waitForText('Shipment Approved')
                ->assertSee('Shipment Approved');
        });
    }

    /**
     * Helper to handle logout if not using a direct link.
     */
    protected function logout(Browser $browser): void
    {
        $browser->ensureNavigation()
            ->click('#user-menu-button')
            ->clickLink('Log Out');
    }
}