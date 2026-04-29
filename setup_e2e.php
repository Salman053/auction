<?php
$u = \App\Models\User::firstOrCreate(['email'=>'test_bidder@example.com'], ['name'=>'Test Bidder', 'password'=>bcrypt('password'), 'role'=>'user']);
if(!$u->wallet) { $u->wallet()->create(['balance_yen'=>0]); }
if(!$u->shippingRate) {
    $r = \App\Models\ShippingRate::firstOrCreate(['name'=>'Test Shipping'], ['fee_yen'=>1000, 'country'=>'Japan']);
    $u->update(['shipping_rate_id' => $r->id]);
}

$admin = \App\Models\User::firstOrCreate(['email'=>'admin@example.com'], ['name'=>'Admin', 'password'=>bcrypt('password'), 'role'=>'admin']);

$a = \App\Models\Auction::create([
    'yahoo_auction_id'=>'test_visual_123',
    'title'=>'Visual E2E Test Auction',
    'starting_bid_yen'=>1000,
    'current_bid_yen'=>1000,
    'bid_count'=>0,
    'status'=>'active',
    'ends_at'=>now()->addMinutes(5),
    'last_synced_at'=>now()
]);
echo "Auction ID: " . $a->id . "\n";
