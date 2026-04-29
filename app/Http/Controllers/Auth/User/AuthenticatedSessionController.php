<?php

namespace App\Http\Controllers\Auth\User;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.user.login');
    }

    /**
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $remember = (bool) ($validated['remember'] ?? false);

        $authenticated = Auth::guard('user')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::User->value,
            'suspended_at' => null,
        ], $remember);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard'))->with('success', 'Welcome back!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }
}
