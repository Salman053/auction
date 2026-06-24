<?php

namespace Tests\Feature\Admin;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_auctions_to_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'admin');

        Auction::factory()->create([
            'title' => 'Test Auction Item A',
            'yahoo_auction_id' => 'a123456789',
            'status' => 'active',
            'ends_at' => now()->addDay(),
        ]);

        Auction::factory()->create([
            'title' => 'Test Auction Item B',
            'yahoo_auction_id' => 'b987654321',
            'status' => 'active',
            'ends_at' => now()->addDay(),
        ]);

        $response = $this->get(route('admin.auctions.export', ['tab' => 'all', 'status' => 'all']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=auctions_export.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Test Auction Item A', $content);
        $this->assertStringContainsString('Test Auction Item B', $content);
        $this->assertStringContainsString('Yahoo Auction ID', $content);
    }
}
