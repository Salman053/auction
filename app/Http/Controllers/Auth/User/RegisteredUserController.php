<?php

namespace App\Http\Controllers\Auth\User;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.user.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->forceFill([
                'role' => UserRole::User->value,
            ])->save();

            $user->wallet()->create([
                'balance_yen' => 0,
                'locked_balance_yen' => 0,
            ]);

            return $user;
        });

        Auth::guard('user')->login($user);

        return redirect()->route('user.dashboard')->with('success', 'Welcome to WatchHub! Your account has been successfully created.');
    }
}
