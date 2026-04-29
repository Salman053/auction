<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request, SettingService $settingService): View
    {
        return view('admin.settings.index', [
            'defaultMultiplier' => $settingService->getInt(SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY, 500),
            'scrapeIntervalMinutes' => $settingService->getInt(SettingService::SCRAPE_INTERVAL_MINUTES_KEY, 5),
            'stripePaymentEnabled' => $settingService->getBool(SettingService::STRIPE_PAYMENT_ENABLED_KEY, false),
        ]);
    }

    public function update(
        SettingUpdateRequest $request,
        SettingService $settingService
    ): RedirectResponse {
        $admin = $request->user('admin');

        $validated = $request->validated();

        $settingService->setInt(
            SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY,
            (int) $validated['default_bidding_multiplier_percent'],
            $admin,
        );

        $settingService->setInt(
            SettingService::SCRAPE_INTERVAL_MINUTES_KEY,
            (int) $validated['scrape_interval_minutes'],
            $admin,
        );

        $settingService->setBool(
            SettingService::STRIPE_PAYMENT_ENABLED_KEY,
            $request->has('stripe_payment_enabled'),
            $admin,
        );

        return back()->with('success', 'System settings saved successfully.');
    }
}
