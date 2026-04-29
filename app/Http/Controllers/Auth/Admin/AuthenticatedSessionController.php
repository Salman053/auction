<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.admin.login');
    }

    /**
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $remember = (bool) ($validated['remember'] ?? false);

        $authenticated = Auth::guard('admin')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::Admin->value,
            'suspended_at' => null,
        ], $remember);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Admin session initialized.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out from admin control.');
    }
}
