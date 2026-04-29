<?php

use App\Services\ScraperService;
use App\Services\YahooAuctionHtmlParser;
use Illuminate\Support\Facades\Config;

uses(Tests\TestCase::class)->in(__DIR__);

it('uses scraper configuration values from config', function () {
    Config::set('scraper.debug', true);
    Config::set('scraper.verify_ssl', false);
    Config::set('scraper.delay', 5);
    Config::set('scraper.max_retries', 7);

    $service = new ScraperService(new YahooAuctionHtmlParser());
    $reflection = new ReflectionClass($service);

    $debugProperty = $reflection->getProperty('debug');
    $debugProperty->setAccessible(true);

    $verifySslProperty = $reflection->getProperty('verifySsl');
    $verifySslProperty->setAccessible(true);

    $delayProperty = $reflection->getProperty('delay');
    $delayProperty->setAccessible(true);

    $maxRetriesProperty = $reflection->getProperty('maxRetries');
    $maxRetriesProperty->setAccessible(true);

    expect($debugProperty->getValue($service))->toBeTrue();
    expect($verifySslProperty->getValue($service))->toBeFalse();
    expect($delayProperty->getValue($service))->toBe(5);
    expect($maxRetriesProperty->getValue($service))->toBe(7);
});
