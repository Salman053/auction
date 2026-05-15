<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->daily();

Schedule::command('auctions:import-yahoo-html', [
    base_path(env('YAHOO_HTML_IMPORT_PATH', 'storage/app/yahoo-html')),
])
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('yahoo:scrape-all --pages=6 --delay=1 --fetch-details --min=0 --max=200')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground();

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

Schedule::command('queue:retry all')
    ->dailyAt('03:00')
    ->withoutOverlapping();

Schedule::command('queue:prune-failed --hours=24')
    ->dailyAt('04:00')
    ->withoutOverlapping();
