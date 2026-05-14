<?php

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('defaults to random sort for user auctions', function () {
    $user = User::factory()->create();
    Auction::factory()->count(5)->create(['status' => 'active']);

    $response = $this->actingAs($user, 'user')->get(route('user.auctions.index'));

    $response->assertStatus(200);
    $response->assertViewHas('filters', function ($filters) {
        return isset($filters['sort']) && $filters['sort'] === 'random';
    });
});

it('defaults to random sort for public auctions', function () {
    Auction::factory()->count(5)->create(['status' => 'active']);

    $response = $this->get(route('auctions.index'));

    $response->assertStatus(200);
    $response->assertViewHas('filters', function ($filters) {
        return isset($filters['sort']) && $filters['sort'] === 'random';
    });
});
