<?php

use App\Services\YahooAuctionHtmlParser;

it('parses auction detail html (offline)', function () {
    $html = <<<'HTML'
<!doctype html>
<html lang="ja">
<head>
    <link rel="canonical" href="https://page.auctions.yahoo.co.jp/jp/auction/abc123XYZ" />
</head>
<body>
    <h1 class="ProductTitle">Seiko Prospex Diver</h1>
    <span class="Price">¥12,345</span>
    <a href="/seller/foo">seller_foo</a>
    <span class="EndTime">2026年4月16日 12時30分</span>
    <img src="https://example.test/img1.jpg" />
    <div class="Rating">98.7</div>
</body>
</html>
HTML;

    $parser = app(YahooAuctionHtmlParser::class);
    $parsed = $parser->parseAuctionDetailHtml($html, null);

    expect($parsed['yahoo_auction_id'])->toBe('abc123XYZ');
    expect($parsed['title'])->toBe('Seiko Prospex Diver');
    expect($parsed['current_bid_yen'])->toBe(12345);
    expect($parsed['seller_name'])->toBe('seller_foo');
    expect($parsed['thumbnail_url'])->toBe('https://example.test/img1.jpg');
    expect($parsed['image_urls'])->toContain('https://example.test/img1.jpg');
    expect($parsed['seller_rating'])->toBe(98.7);
});
