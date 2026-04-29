<?php

namespace Database\Seeders;

use App\Models\Proxy;
use App\Models\ScrapingLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ScrapingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ScrapingLog::query()->count() > 0) {
            return;
        }

        $proxy = Proxy::query()->first();

        ScrapingLog::query()->create([
            'run_uuid' => (string) Str::uuid(),
            'proxy_id' => $proxy?->id,
            'status' => 'success',
            'started_at' => now()->subMinutes(12),
            'ended_at' => now()->subMinutes(11),
            'auctions_created' => 48,
            'auctions_updated' => 0,
            'auctions_closed' => 0,
        ]);
    }
}
