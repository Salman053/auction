<?php

use App\Models\Auction;
use App\Models\User;
use App\Enums\UserRole;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('full auction shipment flow: winning -> bidder confirmation -> admin approval', function () {
    // 1. Setup - Create User and Admin
    $user = User::factory()->create(['role' => UserRole::User->value]);
    $admin = User::factory()->create(['role' => UserRole::Admin->value]);
    
    // 2. Create an ended auction won by the user
    $auction = Auction::factory()->create([
        'status' => 'ended',
        'ends_at' => now()->subDay(),
        'winner_user_id' => $user->id,
        'shipment_status' => 'pending',
    ]);

    // 3. User Step: Confirm Shipment Details
    $this->actingAs($user, 'user')
        ->post(route('user.auctions.confirm-shipment', $auction))
        ->assertRedirect()
        ->assertSessionHas('success');

    $auction->refresh();
    expect($auction->shipment_status)->toBe('bidder_confirmed');
    expect($auction->bidder_confirmed_at)->not->toBeNull();

    // 4. Admin Step: Reject Shipment (for testing the loop)
    $this->actingAs($admin, 'admin')
        ->post(route('admin.auctions.reject-shipment', $auction))
        ->assertRedirect()
        ->assertSessionHas('success');

    $auction->refresh();
    expect($auction->shipment_status)->toBe('pending');
    expect($auction->bidder_confirmed_at)->toBeNull();

    // 5. User Step: Re-Confirm Shipment
    $this->actingAs($user, 'user')
        ->post(route('user.auctions.confirm-shipment', $auction))
        ->assertRedirect();

    $auction->refresh();
    expect($auction->shipment_status)->toBe('bidder_confirmed');

    // 6. Admin Step: Final Approval
    $this->actingAs($admin, 'admin')
        ->post(route('admin.auctions.approve-shipment', $auction))
        ->assertRedirect()
        ->assertSessionHas('success');

    $auction->refresh();
    expect($auction->shipment_status)->toBe('admin_approved');
    expect($auction->admin_approved_at)->not->toBeNull();
});

test('unauthorized users cannot approve shipments', function () {
    $user = User::factory()->create(['role' => UserRole::User->value]);
    $otherUser = User::factory()->create(['role' => UserRole::User->value]);
    
    $auction = Auction::factory()->create([
        'status' => 'ended',
        'winner_user_id' => $user->id,
        'shipment_status' => 'bidder_confirmed',
    ]);

    // Regular user trying to access admin approval
    $this->actingAs($otherUser, 'user')
        ->post(route('admin.auctions.approve-shipment', $auction))
        ->assertRedirect('/admin/login'); 
});
