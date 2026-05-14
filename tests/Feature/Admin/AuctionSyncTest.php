<?php

namespace Tests\Feature\Admin;

use App\Models\Auction;
use App\Models\User;
use App\Jobs\SyncAuctionDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuctionSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_auction_triggers_sync_if_stale()
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'admin');

        $auction = Auction::factory()->create([
            'last_synced_at' => now()->subMinutes(20), // Stale
        ]);

        $response = $this->get(route('admin.auctions.show', $auction));

        $response->assertStatus(200);

        Queue::assertPushed(SyncAuctionDetails::class, function ($job) use ($auction) {
            return $job->auction->id === $auction->id;
        });
    }

    public function test_show_auction_does_not_trigger_sync_if_fresh()
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'admin');

        $auction = Auction::factory()->create([
            'last_synced_at' => now()->subMinutes(5), // Fresh
        ]);

        $response = $this->get(route('admin.auctions.show', $auction));

        $response->assertStatus(200);

        Queue::assertNotPushed(SyncAuctionDetails::class);
    }
}
