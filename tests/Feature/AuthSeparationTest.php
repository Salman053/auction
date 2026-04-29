<?php

use App\Models\User;

it('prevents an admin from using the bidder login', function () {
    $admin = User::factory()->admin()->create([
        'password' => 'password',
    ]);

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');
});

it('prevents a bidder from using the admin login', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->post(route('admin.login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');
});

it('allows an unverified bidder to access the dashboard', function () {
    $user = User::factory()->unverified()->create([
        'password' => 'password',
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->get(route('user.dashboard'))->assertOk();
});

it('requires authentication for the admin dashboard', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
});
