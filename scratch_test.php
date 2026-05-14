<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$auctions = \App\Models\Auction::whereNotNull('image_urls')->limit(50)->get();
foreach ($auctions as $a) {
    echo json_encode($a->image_urls) . PHP_EOL;
}
