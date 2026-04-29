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
    ->everyTenMinutes()
    ->withoutOverlapping();

Schedule::command('yahoo:scrape', ['watch', '--pages=1', '--delay=3', '--fetch-details'])
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('auctions:close')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('auctions:close --force')
    ->hourly()
    ->withoutOverlapping();
