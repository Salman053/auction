<?php

use App\Services\ScraperService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$scraper = app(ScraperService::class);
$res = $scraper->search('seiko', 1);
if (! empty($res)) {
    $yid = $res[0]['yahoo_auction_id'];
    echo 'Found ID: '.$yid."\n";
    $url = 'https://page.auctions.yahoo.co.jp/jp/auction/'.$yid;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);

    if (preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $m)) {
        $json = json_decode(trim($m[1]), true);
        $item = $json['props']['pageProps']['initialState']['item']['detail']['item'] ?? null;
        if ($item) {
            echo 'initPrice: '.var_export($item['initPrice'] ?? null, true)."\n";
            echo 'price: '.var_export($item['price'] ?? null, true)."\n";
            echo 'watchListNum: '.var_export($item['watchListNum'] ?? null, true)."\n";
            echo 'bids: '.var_export($item['bids'] ?? null, true)."\n";
            echo 'biddersNum: '.var_export($item['biddersNum'] ?? null, true)."\n";
            echo 'startTime: '.var_export($item['startTime'] ?? null, true)."\n";
            echo 'endTime: '.var_export($item['endTime'] ?? null, true)."\n";
            echo 'isAutomaticExtension: '.var_export($item['isAutomaticExtension'] ?? null, true)."\n";
            echo 'status: '.var_export($item['status'] ?? null, true)."\n";
            echo 'endStatus: '.var_export($item['endStatus'] ?? null, true)."\n";
        }
    }
}
