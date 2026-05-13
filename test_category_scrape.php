<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

function fetch($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

$url = 'https://auctions.yahoo.co.jp/category/list/';
echo "Fetching {$url}...\n";
$html = fetch($url);
File::put(storage_path('app/yahoo_category_root.html'), $html);

$url = 'https://auctions.yahoo.co.jp/';
echo "Fetching {$url}...\n";
$html = fetch($url);
File::put(storage_path('app/yahoo_home.html'), $html);
