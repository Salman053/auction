<?php

return [
    'debug' => env('SCRAPER_DEBUG', false),
    'verify_ssl' => env('SCRAPER_VERIFY_SSL', true),
    'delay' => env('SCRAPER_DELAY', 2),
    'max_retries' => env('SCRAPER_MAX_RETRIES', 3),
];
