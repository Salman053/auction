<?php

use App\Models\Auction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can filter auctions by search term', function () {
    Auction::factory()->create(['title' => 'Omega Speedmaster', 'status' => 'active']);
    Auction::factory()->create(['title' => 'Rolex Submariner', 'status' => 'active']);

    $response = $this->get(route('auctions.index', ['q' => 'Omega']));

    $response->assertStatus(200);
    $response->assertSee('Omega Speedmaster');
    $response->assertDontSee('Rolex Submariner');
});

it('can filter auctions by price range', function () {
    Auction::factory()->create(['current_bid_yen' => 1000, 'status' => 'active']);
    Auction::factory()->create(['current_bid_yen' => 5000, 'status' => 'active']);
    Auction::factory()->create(['current_bid_yen' => 10000, 'status' => 'active']);

    $response = $this->get(route('auctions.index', [
        'min_price' => 2000,
        'max_price' => 8000,
    ]));

    $response->assertStatus(200);
    $response->assertSeeText('¥5,000');
    $response->assertDontSeeText('¥1,000');
    $response->assertDontSeeText('¥10,000');
});

it('can sort auctions by price ascending', function () {
    Auction::factory()->create(['current_bid_yen' => 10000, 'status' => 'active']);
    Auction::factory()->create(['current_bid_yen' => 1000, 'status' => 'active']);
    Auction::factory()->create(['current_bid_yen' => 5000, 'status' => 'active']);

    $response = $this->get(route('auctions.index', ['sort' => 'price_asc']));

    $response->assertStatus(200);
    $content = $response->getContent();

    // Remove tags to check order of visible text
    $text = strip_tags($content);

    $pos1 = strpos($text, '¥1,000');
    $pos2 = strpos($text, '¥5,000');
    $pos3 = strpos($text, '¥10,000');

    expect($pos1)->not->toBeFalse();
    expect($pos2)->not->toBeFalse();
    expect($pos3)->not->toBeFalse();
    expect($pos1)->toBeLessThan($pos2);
    expect($pos2)->toBeLessThan($pos3);
});

it('hides ended auctions by default', function () {
    // Auction that hasn't ended
    Auction::factory()->create([
        'title' => 'Active Watch',
        'status' => 'active',
        'ends_at' => now()->addDay(),
    ]);

    // Auction that has ended but still has 'active' status in DB
    Auction::factory()->create([
        'title' => 'Ended Watch',
        'status' => 'active',
        'ends_at' => now()->subDay(),
    ]);

    $response = $this->get(route('auctions.index'));

    $response->assertStatus(200);
    $response->assertSee('Active Watch');
    $response->assertDontSee('Ended Watch');
});

it('shows finished auctions when explicitly requested', function () {
    Auction::factory()->create([
        'title' => 'Ended Watch',
        'status' => 'active', // ended by time
        'ends_at' => now()->subDay(),
    ]);

    Auction::factory()->create([
        'title' => 'Closed Watch',
        'status' => 'closed',
        'ends_at' => now()->subDay(),
    ]);

    $response = $this->get(route('auctions.index', ['status' => 'finished']));

    $response->assertStatus(200);
    $response->assertSee('Ended Watch');
    $response->assertSee('Closed Watch');
});
