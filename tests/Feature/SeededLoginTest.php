<?php

use Database\Seeders\AdminUserSeeder;
use Database\Seeders\BidderUserSeeder;
use Illuminate\Support\Facades\Auth;

test('seeded admin can login', function () {
    $this->seed(AdminUserSeeder::class);

    $response = $this->post(route('admin.login.store'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    expect(Auth::guard('admin')->check())->toBeTrue();
});

test('seeded user can login', function () {
    $this->seed(BidderUserSeeder::class);

    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('user.dashboard'));
    expect(Auth::guard('user')->check())->toBeTrue();
});

test('seeded user can login with remember me', function () {
    $this->seed(BidderUserSeeder::class);

    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('user.dashboard'));
    expect(Auth::guard('user')->check())->toBeTrue();

    // Check if the remember cookie exists
    $cookie = collect($response->headers->getCookies())->first(fn ($c) => str_starts_with($c->getName(), 'remember_user_'));
    expect($cookie)->not->toBeNull();
});
