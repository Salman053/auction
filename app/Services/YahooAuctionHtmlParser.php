<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class YahooAuctionHtmlParser
{
    public function __construct() {}

    /**
     * Parse Yahoo search results using a hybrid approach (Regex + DOM).
     */
    public function parseSearchResults(string $html): array
    {
        $results = [];

        // Try DOM parsing first
        $results = $this->parseWithDom($html);

        // Fallback to Regex if DOM fails
        if (empty($results)) {
            $results = $this->parseWithRegex($html);
        }

        return $results;
    }

    /**
     * Extract auction data from a DOM node safely.
     */
    private function extractAuctionData(DOMXPath $xpath, \DOMNode $node): ?array
    {
        $linkNode = $xpath->query(".//a[contains(@href, '/auction/')]", $node)->item(0);
        if (! $linkNode instanceof \DOMElement) {
            return null;
        }

        $href = $linkNode->getAttribute('href');
        if (! preg_match('/auction\/([a-zA-Z0-9]+)/', $href, $matches)) {
            return null;
        }

        $auctionId = $matches[1];

        $title = $this->firstText($xpath, [
            ".//a[contains(@class, 'Product__titleLink')]",
            ".//a[contains(@class, 'title')]",
            './/h3',
            ".//span[contains(@class, 'Title')]",
        ], $node);

        $priceText = $this->firstText($xpath, [
            ".//span[contains(@class, 'Price')]",
            ".//span[contains(@class, 'price')]",
            ".//div[contains(@class, 'Price')]",
        ], $node);

        $price = $this->parsePrice($priceText);

        // Extract ends_at from data attributes if available (very reliable in search results)
        $endsAt = null;
        $bonusNode = $xpath->query(".//*[contains(@data-auction-endtime, '')]", $node)->item(0);
        if ($bonusNode instanceof \DOMElement) {
            $timestamp = $bonusNode->getAttribute('data-auction-endtime');
            if (is_numeric($timestamp) && intval($timestamp) > 0) {
                $endsAt = CarbonImmutable::createFromTimestamp(intval($timestamp));
            }
        }

        if (! $endsAt) {
            $timeNode = $xpath->query(".//*[contains(@class, 'Product__time') or contains(@class, 'time')]", $node)->item(0);
            if ($timeNode) {
                $endsAt = $this->parseJapaneseDateString($timeNode->nodeValue);
            }
        }

        return [
            'yahoo_auction_id' => $auctionId,
            'title' => $this->cleanText($title),
            'current_bid_yen' => $price,
            'ends_at' => $endsAt,
            'thumbnail_url' => $this->extractThumbnail($xpath, $node),
            'link' => $href,
        ];
    }

    /**
     * Fallback Regex parsing for when Yahoo serves restricted HTML.
     */
    private function parseWithRegex(string $html): array
    {
        $results = [];

        if (preg_match_all('/\/auction\/([a-zA-Z0-9]+)/', $html, $matches)) {
            $ids = array_unique($matches[1]);
            foreach ($ids as $id) {
                // Find title near the ID
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $tm)) {
                    $results[] = [
                        'yahoo_auction_id' => $id,
                        'title' => 'Auction '.$id,
                        'current_bid_yen' => 0,
                        'ends_at' => null,
                        'thumbnail_url' => null,
                        'link' => "https://page.auctions.yahoo.co.jp/jp/auction/{$id}",
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Parse auction detail HTML.
     */
    public function parseAuctionDetailHtml(string $html, ?string $sourceUrl = null): array
    {
        $xpath = $this->xpath($html);

        $bodyText = $this->firstText($xpath, ['//body']);
        if (str_contains($bodyText, 'アクセスが制限') || str_contains($bodyText, 'Access restricted')) {
            return ['error' => 'blocked', 'message' => 'Access restricted by Yahoo'];
        }

        $auctionId = null;
        if ($sourceUrl && preg_match('/auction\/([a-zA-Z0-9]+)/', $sourceUrl, $matches)) {
            $auctionId = $matches[1];
        }

        if ($auctionId === null) {
            $auctionId = $this->extractAuctionIdFromCanonical($xpath);
        }

        $title = $this->firstText($xpath, [
            "//h1[contains(@class, 'ProductTitle')]",
            "//h1[contains(@class, 'ItemTitle')]",
            '//h1',
            '//title',
        ]);

        $priceText = $this->firstText($xpath, [
            "//span[contains(@class, 'Price')]",
            "//dd[contains(@class, 'price')]",
            "//div[contains(@class, 'CurrentPrice')]",
            "//*[contains(text(), '現在')]/following-sibling::*",
        ]);

        $price = $this->parsePrice($priceText);

        $sellerData = $this->extractSellerInfo($xpath, $html);

        // FIXED: Enhanced end date extraction
        $endsAt = $this->extractEndDate($xpath, $html);

        $isEnded = str_contains($html, '終了しました') || str_contains($html, 'オークションは終了') || str_contains($html, 'This auction has ended');

        return [
            'yahoo_auction_id' => $auctionId,
            'title' => $title ? $this->cleanText($title) : null,
            'current_bid_yen' => $price ?: null,
            'ends_at' => $endsAt,
            'status' => $isEnded ? 'finished' : 'active',
            'seller_name' => $sellerData['name'] ?? null,
            'yahoo_seller_id' => $sellerData['id'] ?? null,
            'seller_rating' => $sellerData['rating'] ?? null,
            'watcher_count' => $sellerData['watch_count'] ?? null,
            'thumbnail_url' => $this->extractMainImage($xpath),
            'image_urls' => $this->extractImageUrls($xpath),
            'raw' => array_merge($sellerData['raw'] ?? [], [
                'source_url' => $sourceUrl,
                'scraped_at' => now()->toDateTimeString(),
                'title_raw' => $title,
                'price_text' => $priceText,
                'is_ended_detected' => $isEnded,
            ]),
        ];
    }

    private function parseWithDom(string $html): array
    {
        $results = [];
        $seenIds = [];
        $xpath = $this->xpath($html);

        $strategies = [
            "//li[contains(@class, 'Product')]",
            "//div[contains(@class, 'Product')]",
            "//div[contains(@class, 'item')]",
            "//a[contains(@href, '/auction/')]/ancestor::li[1]",
        ];

        foreach ($strategies as $strategy) {
            $nodes = $xpath->query($strategy);
            if ($nodes && $nodes->length > 0) {
                foreach ($nodes as $node) {
                    $item = $this->extractAuctionData($xpath, $node);
                    // ADD THE DUPLICATE CHECK HERE
                    if ($item && ! empty($item['yahoo_auction_id']) && ! in_array($item['yahoo_auction_id'], $seenIds)) {
                        $seenIds[] = $item['yahoo_auction_id'];  // Mark as seen
                        $results[] = $item;
                    }
                }
                if (! empty($results)) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Robust seller information extraction.
     */
    private function extractSellerInfo(DOMXPath $xpath, string $html): array
    {
        // JSON extraction (most reliable)
        if (preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
            try {
                $json = json_decode(trim($matches[1]), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $item = $json['props']['pageProps']['initialState']['item']['detail']['item'] ?? null;
                    if ($item && isset($item['seller'])) {
                        $seller = $item['seller'];
                        $watchCount = $item['watchCount'] ?? null;

                        return [
                            'name' => $seller['displayName'] ?? null,
                            'id' => $seller['id'] ?? null,
                            'rating' => isset($seller['rating']['goodRating']) ? (float) str_replace('%', '', $seller['rating']['goodRating']) : null,
                            'watch_count' => $watchCount,
                            'raw' => [
                                'seller_json' => $seller,
                                'item_json_partial' => array_intersect_key($item, array_flip(['id', 'title', 'price', 'category', 'condition', 'watchCount'])),
                            ],
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Yahoo Scraper: JSON parsing error: '.$e->getMessage());
            }
        }

        // DOM extraction for name
        $name = $this->firstText($xpath, [
            "//a[contains(@href, '/seller/')]",
            "//*[contains(@class, 'Seller__name')]",
            "//*[contains(@class, 'seller-name')]",
            "//div[contains(@class, 'Seller')]//a",
        ]);

        // Seller ID from regex (still useful)
        $sellerId = null;
        if (preg_match('/auctions\.yahoo\.co\.jp\/seller\/([^"\'>\s\?\/]+)/', $html, $m)) {
            $sellerId = $m[1];
        }

        // Rating extraction
        $rating = $this->parseSellerRating($xpath);
        if ($rating === null && preg_match('/(\d+(?:\.\d+)?)\s*%/', $html, $m)) {
            $rating = (float) $m[1];
        }

        // Watcher count from DOM
        $watchCount = $this->extractWatcherCount($xpath, $html);

        return [
            'name' => $name ? $this->cleanText($name) : null,
            'id' => $sellerId,
            'rating' => $rating,
            'watch_count' => $watchCount,
            'raw' => [
                'seller_name_dom' => $name,
                'seller_id_regex' => $sellerId,
                'seller_rating_parsed' => $rating,
                'watch_count_parsed' => $watchCount,
            ],
        ];
    }

    /**
     * Extract shipping fee information.
     */
    private function extractShippingFee(DOMXPath $xpath, string $html): ?int
    {
        $freeShippingKeywords = ['送料無料', '出品者負担'];
        foreach ($freeShippingKeywords as $kw) {
            if (str_contains($html, $kw)) {
                return 0;
            }
        }

        $shippingSelectors = [
            "//*[contains(@class, 'Price__shipping')]",
            "//*[contains(@class, 'shipping-fee')]",
            "//dt[contains(text(), '送料')]/following-sibling::dd",
        ];

        foreach ($shippingSelectors as $selector) {
            $text = $this->firstText($xpath, [$selector]);
            if ($text && preg_match('/(\d{1,3}(?:,\d{3})*)\s*円/', $text, $matches)) {
                return (int) str_replace(',', '', $matches[1]);
            }
        }

        return null;
    }

    /**
     * Enhanced end date extraction with multiple methods
     */
    private function extractEndDate(DOMXPath $xpath, string $html): ?CarbonImmutable
    {
        $endDate = null;

        // Method 0: Look for JSON data (most reliable)
        if (preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
            try {
                $json = json_decode(trim($matches[1]), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $item = $json['props']['pageProps']['initialState']['item']['detail']['item'] ?? null;
                    if ($item && isset($item['endTime'])) {
                        // Yahoo JSON endTime is usually ISO8601 or similar, assume JST if no TZ
                        return CarbonImmutable::parse($item['endTime'], 'Asia/Tokyo')->setTimezone('UTC');
                    }
                    if ($item && isset($item['closeTime'])) {
                        return CarbonImmutable::createFromTimestamp(intval($item['closeTime']) / 1000);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        // Method 1: Look for countdown timer data
        $endDate = $this->extractFromCountdownData($xpath);
        if ($endDate) {
            return $endDate;
        }

        // Method 2: Look for end time in meta tags
        $endDate = $this->extractFromMetaTags($xpath);
        if ($endDate) {
            return $endDate;
        }

        // Method 3: Look for JavaScript variables
        $endDate = $this->extractFromJavaScript($html);
        if ($endDate) {
            return $endDate;
        }

        // Method 4: Look for visible end time text with multiple patterns
        $endDate = $this->extractFromVisibleText($xpath);
        if ($endDate) {
            return $endDate;
        }

        // Method 5: Direct regex on HTML as last resort
        $endDate = $this->extractFromHtmlRegex($html);
        if ($endDate) {
            return $endDate;
        }

        return null;
    }

    /**
     * Extract end date from countdown data
     */
    private function extractFromCountdownData(DOMXPath $xpath): ?CarbonImmutable
    {
        $countdownSelectors = [
            "//span[@class='Countdown']",
            "//div[@class='EndTime']",
            '//span[@data-endtime]',
            '//span[@data-auction-endtime]',
            '//*[@data-auction-endtime]',
            '//time[@datetime]',
            "//*[contains(@class, 'countdown')]",
            "//*[contains(@class, 'endtime')]",
        ];

        foreach ($countdownSelectors as $selector) {
            $nodes = $xpath->query($selector);
            if (! $nodes instanceof \DOMNodeList) {
                continue;
            }

            foreach ($nodes as $node) {
                if ($node instanceof \DOMElement) {
                    // Check for data-auction-endtime (common in search and some detail pages)
                    if ($node->hasAttribute('data-auction-endtime')) {
                        $timestamp = $node->getAttribute('data-auction-endtime');
                        if (is_numeric($timestamp) && intval($timestamp) > 0) {
                            return CarbonImmutable::createFromTimestamp(intval($timestamp));
                        }
                    }

                    // Check for data-endtime attribute
                    if ($node->hasAttribute('data-endtime')) {
                        $timestamp = $node->getAttribute('data-endtime');
                        if (is_numeric($timestamp) && intval($timestamp) > 0) {
                            return CarbonImmutable::createFromTimestamp(intval($timestamp));
                        }
                    }

                    // Check for datetime attribute
                    if ($node->hasAttribute('datetime')) {
                        $datetime = $node->getAttribute('datetime');
                        try {
                            // Yahoo Japan times are in JST
                            return CarbonImmutable::parse($datetime, 'Asia/Tokyo')->setTimezone('UTC');
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract from meta tags
     */
    private function extractFromMetaTags(DOMXPath $xpath): ?CarbonImmutable
    {
        $metaSelectors = [
            "//meta[@property='auction:end_time']",
            "//meta[@name='endtime']",
            "//meta[@property='article:modified_time']",
        ];

        foreach ($metaSelectors as $selector) {
            $node = $xpath->query($selector)->item(0);
            if ($node instanceof \DOMElement && $node->hasAttribute('content')) {
                $content = $node->getAttribute('content');
                try {
                    // Yahoo Japan times are in JST
                    return CarbonImmutable::parse($content, 'Asia/Tokyo')->setTimezone('UTC');
                } catch (\Exception $e) {
                }
            }
        }

        return null;
    }

    /**
     * Extract from JavaScript variables
     */
    private function extractFromJavaScript(string $html): ?CarbonImmutable
    {
        $patterns = [
            '/endTime["\']?\s*[:=]\s*["\'](\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2})["\']/i',
            '/end_date["\']?\s*[:=]\s*["\'](\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2})["\']/i',
            '/auctionEnd["\']?\s*[:=]\s*(\d{10,13})/i',
            '/closeTime["\']?\s*[:=]\s*("|\')?(\d{10,13})("|\')?/i',
            '/Y\.J\.Auction\.endTime\s*=\s*(\d+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $value = $matches[1];
                if (is_numeric($value)) {
                    if (strlen($value) === 13) {
                        return CarbonImmutable::createFromTimestamp(intval($value) / 1000);
                    } elseif (strlen($value) === 10) {
                        return CarbonImmutable::createFromTimestamp(intval($value));
                    }
                } else {
                    try {
                        return CarbonImmutable::parse($value, 'Asia/Tokyo')->setTimezone('UTC');
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract from visible text with comprehensive patterns
     */
    private function extractFromVisibleText(DOMXPath $xpath): ?CarbonImmutable
    {
        $textPatterns = [
            "//*[contains(text(), '終了時間')]",
            "//*[contains(text(), '終了日時')]",
            "//*[contains(text(), 'オークション終了')]",
            "//*[contains(text(), '残り時間')]",
            "//*[contains(@class, 'endDate')]",
            "//*[contains(@class, 'closeDate')]",
        ];

        foreach ($textPatterns as $pattern) {
            $node = $xpath->query($pattern)->item(0);
            if ($node) {
                $text = $node->nodeValue;
                $date = $this->parseJapaneseDateString($text);
                if ($date) {
                    return $date;
                }

                // Try parent node if current node doesn't contain the date
                $parent = $node->parentNode;
                if ($parent) {
                    $date = $this->parseJapaneseDateString($parent->nodeValue);
                    if ($date) {
                        return $date;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract using regular expressions on entire HTML
     */
    private function extractFromHtmlRegex(string $html): ?CarbonImmutable
    {
        $patterns = [
            '/(\d{4}[-\/年]\d{1,2}[-\/月]\d{1,2}日?)\s+(\d{1,2}[:時]\d{1,2}分?)/u',
            '/終了[時間:：]\s*(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})\s+(\d{1,2}:\d{1,2})/u',
            '/(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2})/',
            '/(\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2})/',
            '/unixtime["\']?\s*[:=]\s*(\d{10,13})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                if (isset($matches[2]) && isset($matches[1])) {
                    // Combined date and time match
                    $dateTimeStr = $matches[1].' '.$matches[2];

                    return $this->parseJapaneseDateString($dateTimeStr);
                } elseif (isset($matches[1])) {
                    // Single match (could be timestamp or datetime)
                    if (is_numeric($matches[1])) {
                        $timestamp = intval($matches[1]);
                        if ($timestamp > 1000000000) {
                            if ($timestamp > 10000000000) {
                                return CarbonImmutable::createFromTimestamp($timestamp / 1000);
                            }

                            return CarbonImmutable::createFromTimestamp($timestamp);
                        }
                    } else {
                        return $this->parseJapaneseDateString($matches[1]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Parse Japanese date string with improved handling, including relative times.
     */
    private function parseJapaneseDateString(string $dateString): ?CarbonImmutable
    {
        $originalString = trim($dateString);
        if (empty($originalString)) {
            return null;
        }

        // Clean common Japanese characters for easier regex matching
        $search = ['残り', '時間', '分', '秒', '日', ' ', '　'];
        $replace = ['', 'h', 'm', 's', 'd', '', ''];
        $normalized = str_replace($search, $replace, $originalString);

        // Relative time detection: Look for duration units (d, h, m, s)
        // Yahoo shows "3日", "4時間", "15分", etc.
        if (preg_match('/(\d+d|\d+h|\d+m|\d+s)/', $normalized)) {
            $now = CarbonImmutable::now();
            $isRelative = false;
            $days = 0;
            $hours = 0;
            $minutes = 0;
            $seconds = 0;

            if (preg_match('/(\d+)d/', $normalized, $m)) {
                $days = intval($m[1]);
                $isRelative = true;
            }
            if (preg_match('/(\d+)h/', $normalized, $m)) {
                $hours = intval($m[1]);
                $isRelative = true;
            }
            if (preg_match('/(\d+)m/', $normalized, $m)) {
                $minutes = intval($m[1]);
                $isRelative = true;
            }
            if (preg_match('/(\d+)s/', $normalized, $m)) {
                $seconds = intval($m[1]);
                $isRelative = true;
            }

            // Handle HH:MM:SS or HH:MM format IF it was in a "Remaining" context
            // But usually Yahoo shows "X時間" or "X分"
            if (! $isRelative && preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?/', $normalized, $m)) {
                $hours = intval($m[1]);
                $minutes = intval($m[2]);
                $seconds = isset($m[3]) ? intval($m[3]) : 0;
                $isRelative = true;
            }

            if ($isRelative) {
                // If it's just "X days", Yahoo often means "X days and some hours"
                // but we stick to the provided minimum for safety in bidding.
                return $now->addDays($days)
                    ->addHours($hours)
                    ->addMinutes($minutes)
                    ->addSeconds($seconds);
            }
        }

        // Absolute date parsing
        $replacements = [
            '年' => '-',
            '月' => '-',
            '日' => '',
            '時' => ':',
            '分' => '',
            '秒' => '',
            '終了時間' => '',
            '終了日時' => '',
            '終了' => '',
            'まで' => '',
            '残り時間' => '',
            '(' => '',
            ')' => '',
            '【' => '',
            '】' => '',
            '/' => '-',
        ];

        // Also remove day of week like (水), (木) etc.
        $cleaned = preg_replace('/\([月火水木金土日]\)/u', '', $originalString);
        $cleaned = str_replace(array_keys($replacements), array_values($replacements), $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        // Try different formats
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-n-j H:i:s',
            'Y-n-j H:i',
            'm-d H:i',
            'n-j H:i',
            'H:i:s',
            'H:i',
        ];

        foreach ($formats as $format) {
            try {
                // Yahoo Japan times are in JST (Asia/Tokyo)
                $date = CarbonImmutable::createFromFormat($format, $cleaned, 'Asia/Tokyo');

                if ($date) {
                    // If format was just H:i or m-d H:i, make sure we have the correct year
                    // createFromFormat uses current year/month/day for missing parts in the JST timezone.

                    // If the parsed date is significantly in the past (e.g. yesterday's H:i),
                    // it might actually be for tomorrow if it's an end time.
                    // But usually Yahoo shows "5/14" if it's tomorrow.

                    return $date->setTimezone('UTC');
                }
            } catch (\Exception $e) {
            }
        }

        // Try direct parse as last resort
        try {
            $date = CarbonImmutable::parse($cleaned, 'Asia/Tokyo');

            return $date->setTimezone('UTC');
        } catch (\Exception $e) {
        }

        return null;
    }

    private function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        // Load with UTF-8 support
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function firstText(DOMXPath $xpath, array $queries, ?\DOMNode $contextNode = null): string
    {
        foreach ($queries as $query) {
            $node = $xpath->query($query, $contextNode)->item(0);
            if ($node) {
                return trim($node->nodeValue);
            }
        }

        return '';
    }

    private function extractThumbnail(DOMXPath $xpath, \DOMNode $node): ?string
    {
        $img = $xpath->query('.//img', $node)->item(0);
        if ($img instanceof \DOMElement) {
            $src = $img->getAttribute('src');
            if ($src && ! str_starts_with($src, 'data:')) {
                $lowerUrl = strtolower($src);
                if (! str_contains($lowerUrl, 'buyee') &&
                    ! str_contains($lowerUrl, 's.yimg.jp') &&
                    ! str_contains($lowerUrl, 'banner') &&
                    ! str_contains($lowerUrl, 'promo') &&
                    ! str_contains($lowerUrl, 'logo')) {
                    return $src;
                }
            }
        }

        return null;
    }

    private function extractMainImage(DOMXPath $xpath): ?string
    {
        // Method 1: Try JSON __NEXT_DATA__ (most reliable on modern Yahoo pages)
        $html = $xpath->document->saveHTML();
        if (preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
            try {
                $json = json_decode(trim($matches[1]), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $item = $json['props']['pageProps']['initialState']['item']['detail']['item'] ?? null;
                    if ($item) {
                        $imageList = $item['img'] ?? $item['images'] ?? null;
                        if (is_array($imageList) && ! empty($imageList)) {
                            $first = $imageList[0];

                            return $first['image'] ?? $first['full'] ?? null;
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        // Method 2: DOM fallback
        $img = $xpath->query("//img[contains(@class, 'MainImage') or contains(@class, 'ProductImage')]")->item(0);

        if ($img instanceof \DOMElement) {
            $src = $img->getAttribute('src');

            if ($src !== '') {
                $lowerUrl = strtolower($src);
                if (! str_contains($lowerUrl, 'buyee') &&
                    ! str_contains($lowerUrl, 's.yimg.jp') &&
                    ! str_contains($lowerUrl, 'banner') &&
                    ! str_contains($lowerUrl, 'promo') &&
                    ! str_contains($lowerUrl, 'logo')) {
                    return $src;
                }
            }
        }

        $fallback = $xpath->query('//img')->item(0);
        if ($fallback instanceof \DOMElement) {
            $src = $fallback->getAttribute('src');

            if ($src !== '') {
                $lowerUrl = strtolower($src);
                if (! str_contains($lowerUrl, 'buyee') &&
                    ! str_contains($lowerUrl, 's.yimg.jp') &&
                    ! str_contains($lowerUrl, 'banner') &&
                    ! str_contains($lowerUrl, 'promo') &&
                    ! str_contains($lowerUrl, 'logo')) {
                    return $src;
                }
            }
        }

        return null;
    }

    private function extractImageUrls(DOMXPath $xpath): array
    {
        $urls = [];

        // 1. Try JSON extraction first (most reliable on modern pages)
        $html = $xpath->document->saveHTML();
        if (preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
            try {
                $json = json_decode(trim($matches[1]), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $item = $json['props']['pageProps']['initialState']['item']['detail']['item'] ?? null;
                    if ($item) {
                        // Yahoo changed key from 'images' to 'img' — support both
                        $imageList = $item['img'] ?? $item['images'] ?? null;
                        if (is_array($imageList)) {
                            foreach ($imageList as $imgObj) {
                                // Only store the full-res 'image', not the 'thumbnail'
                                if (isset($imgObj['image'])) {
                                    $urls[] = $imgObj['image'];
                                } elseif (isset($imgObj['full'])) {
                                    $urls[] = $imgObj['full'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        // If JSON extraction succeeded, skip DOM fallback to avoid thumbnail duplicates
        if (! empty($urls)) {
            return array_slice(array_unique($urls), 0, 20);
        }

        // 2. DOM extraction (fallback or addition)
        $images = $xpath->query('//img');
        if ($images) {
            foreach ($images as $img) {
                if ($img instanceof \DOMElement) {
                    // Check multiple attributes for images (lazy loading support)
                    $src = $img->getAttribute('src') ?: $img->getAttribute('data-src') ?: $img->getAttribute('data-original');

                    if ($src && ! str_starts_with($src, 'data:')) {
                        $lowerUrl = strtolower($src);

                        // Refined filter: Block logos/banners but allow auction images even if on s.yimg.jp
                        // Real auction images usually have 'auc' or 'images.auctions' in path
                        $isBlacklisted = str_contains($lowerUrl, 'buyee') ||
                                        str_contains($lowerUrl, 'banner') ||
                                        str_contains($lowerUrl, 'promo') ||
                                        str_contains($lowerUrl, 'logo') ||
                                        (str_contains($lowerUrl, 's.yimg.jp') && ! str_contains($lowerUrl, 'auc'));

                        if (! $isBlacklisted) {
                            $urls[] = $src;
                        }
                    }
                }
            }
        }

        return array_slice(array_unique($urls), 0, 20);
    }

    private function extractAuctionIdFromCanonical(DOMXPath $xpath): ?string
    {
        $canonical = $xpath->query("//link[@rel='canonical']")->item(0);
        if (! $canonical instanceof \DOMElement) {
            return null;
        }

        $href = (string) $canonical->getAttribute('href');
        if ($href === '') {
            return null;
        }

        if (preg_match('/auction\/([a-zA-Z0-9]+)/', $href, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    private function parseSellerRating(DOMXPath $xpath): ?float
    {
        $text = $this->firstText($xpath, [
            "//*[contains(@class, 'Seller__ratingPercentage')]",
            "//*[contains(@class, 'Rating') or contains(@class, 'rating')]",
            "//a[contains(@href, 'show/rating')]//span",
        ]);

        if ($text === '') {
            // Try to find text containing %
            $node = $xpath->query("//*[contains(text(), '%')]")->item(0);
            if ($node) {
                $text = $node->nodeValue;
            }
        }

        if ($text === '') {
            return null;
        }

        if (preg_match('/(\d+(?:\.\d+)?)/', $text, $matches) !== 1) {
            return null;
        }

        return (float) $matches[1];
    }

    private function extractWatcherCount(DOMXPath $xpath, string $html): ?int
    {
        $selectors = [
            "//*[contains(@class, 'Watchlist__count')]",
            "//*[contains(@class, 'watchlist-count')]",
            "//*[contains(text(), 'ウォッチリスト')]/following-sibling::*",
        ];

        foreach ($selectors as $selector) {
            $text = $this->firstText($xpath, [$selector]);
            if ($text && preg_match('/(\d+)/', $text, $matches)) {
                return (int) $matches[1];
            }
        }

        // Regex fallback
        if (preg_match('/"watchCount":\s*(\d+)/', $html, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * Parse category list from HTML.
     */
    public function parseCategoryList(string $html): array
    {
        $categories = [];
        $xpath = $this->xpath($html);

        // Try parsing from the "Filter" section (common in search/category pages)
        $nodes = $xpath->query("//div[contains(@class, 'Filter')]//a[contains(@href, '/category/list/')]");

        if ($nodes->length === 0) {
            // Try fallback for top-level pages or different layouts
            $nodes = $xpath->query("//a[contains(@href, '/category/list/') or contains(@href, '-category.html')]");
        }

        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $href = $node->getAttribute('href');
            $name = trim($node->nodeValue);

            // Remove counts like "(123)" from name
            $name = preg_replace('/\(\d+(?:,\d+)*\)$/', '', $name);
            $name = trim($name);

            if (empty($name) || $name === 'さらに絞り込む' || $name === '関連カテゴリ') {
                continue;
            }

            $yahooId = null;
            if (preg_match('/category\/list\/(\d+)/', $href, $matches)) {
                $yahooId = $matches[1];
            } elseif (preg_match('/\/(\d+)-category\.html/', $href, $matches)) {
                $yahooId = $matches[1];
            }

            if ($yahooId) {
                $categories[$yahooId] = [
                    'yahoo_category_id' => $yahooId,
                    'name' => $name,
                    'url' => $href,
                ];
            }
        }

        return array_values($categories);
    }

    /**
     * Clean and normalize text from HTML.
     */
    private function cleanText(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $text = strip_tags($text);

        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Parse price string into integer, handling "tax included" and other suffixes.
     */
    private function parsePrice(?string $text): int
    {
        if (! $text) {
            return 0;
        }

        // Match the first number sequence (e.g. "840,000")
        if (preg_match('/([0-9,]+)/', $text, $matches)) {
            return (int) str_replace(',', '', $matches[1]);
        }

        return 0;
    }
}
