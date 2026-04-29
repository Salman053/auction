<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('role', UserRole::Admin)->first();

        $settingService = app(SettingService::class);
        $settingService->setInt(SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY, 500, $admin);
        $settingService->setInt(SettingService::SCRAPE_INTERVAL_MINUTES_KEY, 5, $admin);
    }
}
