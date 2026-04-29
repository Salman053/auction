<?php

use App\Models\Auction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can filter auctions by search term', function () {
    Auction::factory()->create(['title' => 'Omega Speedmaster']);
    Auction::factory()->create(['title' => 'Rolex Submariner']);

    $response = $this->get(route('auctions.index', ['q' => 'Omega']));

    $response->assertStatus(200);
    $response->assertSee('Omega Speedmaster');
    $response->assertDontSee('Rolex Submariner');
});

it('can filter auctions by price range', function () {
    Auction::factory()->create(['current_bid_yen' => 1000]);
    Auction::factory()->create(['current_bid_yen' => 5000]);
    Auction::factory()->create(['current_bid_yen' => 10000]);

    $response = $this->get(route('auctions.index', [
        'min_price' => 2000,
        'max_price' => 8000,
    ]));

    $response->assertStatus(200);
    $response->assertSee('¥5,000');
    $response->assertDontSee('¥1,000');
    $response->assertDontSee('¥10,000');
});

it('can sort auctions by price ascending', function () {
    Auction::factory()->create(['current_bid_yen' => 10000]);
    Auction::factory()->create(['current_bid_yen' => 1000]);
    Auction::factory()->create(['current_bid_yen' => 5000]);

    $response = $this->get(route('auctions.index', ['sort' => 'price_asc']));

    $response->assertStatus(200);
    $content = $response->getContent();
    
    $pos1 = strpos($content, '¥1,000');
    $pos2 = strpos($content, '¥5,000');
    $pos3 = strpos($content, '¥10,000');

    expect($pos1)->toBeLessThan($pos2);
    expect($pos2)->toBeLessThan($pos3);
});

it('can sort auctions by price descending', function () {
    Auction::factory()->create(['current_bid_yen' => 10000]);
    Auction::factory()->create(['current_bid_yen' => 1000]);
    Auction::factory()->create(['current_bid_yen' => 5000]);

    $response = $this->get(route('auctions.index', ['sort' => 'price_desc']));

    $response->assertStatus(200);
    $content = $response->getContent();
    
    $pos1 = strpos($content, '¥10,000');
    $pos2 = strpos($content, '¥5,000');
    $pos3 = strpos($content, '¥1,000');

    expect($pos1)->toBeLessThan($pos2);
    expect($pos2)->toBeLessThan($pos3);
});
