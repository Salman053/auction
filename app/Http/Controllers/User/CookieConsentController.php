<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'accepted' => ['required', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);

        $accepted = (bool) $validated['accepted'];
        $settings = $validated['settings'] ?? [];

        if ($user = $request->user()) {
            $user->update([
                'cookies_accepted' => $accepted,
                'cookies_settings' => $settings,
                'cookies_accepted_at' => now(),
            ]);
        }

        // Also set a cookie so the popup doesn't show up again for this session/device
        $cookie = Cookie::make('cookie_consent', json_encode([
            'accepted' => $accepted,
            'settings' => $settings,
        ]), 60 * 24 * 365); // 1 year

        return response()->json([
            'success' => true,
            'message' => 'Consent stored successfully.',
        ])->withCookie($cookie);
    }
}
