<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->daily();

Schedule::command('auctions:import-yahoo-html', [
    env('YAHOO_HTML_IMPORT_PATH', storage_path('app/yahoo-html')),
])
    ->everyMinute()
    ->withoutOverlapping();

// Increase scraping frequency to every minute for real-time updates
Schedule::command('yahoo:scrape', ['watch', '--pages=1', '--delay=3', '--fetch-details'])
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Notify users about auctions ending soon
Schedule::command('auctions:notify-ending-soon')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('auctions:close')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('auctions:close --force')
    ->hourly()
    ->withoutOverlapping();

// Failure recovery: Retry failed jobs daily to ensure system integrity
Schedule::command('queue:retry all')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Cleanup: Prune failed jobs older than 24 hours
Schedule::command('queue:prune-failed --hours=24')
    ->dailyAt('04:00')
    ->withoutOverlapping();
