<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class SettingService
{
    public const DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY = 'default_bidding_multiplier_percent';

    public const SCRAPE_INTERVAL_MINUTES_KEY = 'scrape_interval_minutes';

    public const STRIPE_PAYMENT_ENABLED_KEY = 'stripe_payment_enabled';

    public function getBool(string $key, bool $default): bool
    {
        $setting = Setting::query()->where('key', $key)->first();

        $value = is_array($setting?->value) ? ($setting->value['value'] ?? null) : null;
        if (is_bool($value)) {
            return $value;
        }

        return $default;
    }

    public function setBool(string $key, bool $value, ?User $actor = null): Setting
    {
        return Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => ['value' => $value], 'updated_by_user_id' => $actor?->id],
        );
    }

    public function getInt(string $key, int $default): int
    {
        $setting = Setting::query()->where('key', $key)->first();

        $value = is_array($setting?->value) ? ($setting->value['value'] ?? null) : null;
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    public function setInt(string $key, int $value, ?User $actor = null): Setting
    {
        $setting = Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => ['value' => $value], 'updated_by_user_id' => $actor?->id],
        );

        return $setting;
    }
}
