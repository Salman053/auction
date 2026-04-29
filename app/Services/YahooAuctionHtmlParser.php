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

        $price = (int) preg_replace('/[^0-9]/', '', $priceText);

        return [
            'yahoo_auction_id' => $auctionId,
            'title' => $this->cleanText($title),
            'current_bid_yen' => $price,
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

        $price = (int) preg_replace('/[^0-9]/', '', $priceText);

        $sellerData = $this->extractSellerInfo($xpath, $html);

        // FIXED: Enhanced end date extraction
        $endsAt = $this->extractEndDate($xpath, $html);

        return [
            'yahoo_auction_id' => $auctionId,
            'title' => $title ? $this->cleanText($title) : null,
            'current_bid_yen' => $price ?: null,
            'shipping_fee_yen' => $this->extractShippingFee($xpath, $html),
            'ends_at' => $endsAt,
            'seller_name' => $sellerData['name'] ?? null,
            'yahoo_seller_id' => $sellerData['id'] ?? null,
            'seller_rating' => $sellerData['rating'] ?? null,
            'thumbnail_url' => $this->extractMainImage($xpath),
            'image_urls' => $this->extractImageUrls($xpath),
            'raw' => ['source_url' => $sourceUrl],
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

                        return [
                            'name' => $seller['displayName'] ?? null,
                            'id' => $seller['id'] ?? null,
                            'rating' => isset($seller['rating']['goodRating']) ? (float) str_replace('%', '', $seller['rating']['goodRating']) : null,
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

        return [
            'name' => $name ? $this->cleanText($name) : null,
            'id' => $sellerId,
            'rating' => $rating,
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
            '//time[@datetime]',
            "//*[contains(@class, 'countdown')]",
            "//*[contains(@class, 'endtime')]",
        ];

        foreach ($countdownSelectors as $selector) {
            $nodes = $xpath->query($selector);
            if (! $nodes instanceof \DOMNodeList) {
                continue;
            }

            $node = $nodes->item(0);
            if ($node instanceof \DOMElement) {
                // Check for data attribute
                if ($node->hasAttribute('data-endtime')) {
                    $timestamp = $node->getAttribute('data-endtime');
                    if (is_numeric($timestamp)) {
                        return CarbonImmutable::createFromTimestamp(intval($timestamp));
                    }
                }

                // Check for datetime attribute
                if ($node->hasAttribute('datetime')) {
                    $datetime = $node->getAttribute('datetime');
                    try {
                        return CarbonImmutable::parse($datetime);
                    } catch (\Exception $e) {
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
                    return CarbonImmutable::parse($content);
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
                        return CarbonImmutable::parse($value);
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
     * Parse Japanese date string with improved handling
     */
    private function parseJapaneseDateString(string $dateString): ?CarbonImmutable
    {
        // Clean the string
        $dateString = trim($dateString);

        // Remove common Japanese text
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
        ];

        $cleaned = str_replace(array_keys($replacements), array_values($replacements), $dateString);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        // Try different formats
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-n-j H:i:s',
            'Y-n-j H:i',
            'Y/m/d H:i:s',
            'Y/m/d H:i',
            'Y-m-d\TH:i:s',
            'Y-m-d\TH:i',
            'Y年n月j日 H:i',
        ];

        foreach ($formats as $format) {
            try {
                $date = CarbonImmutable::createFromFormat($format, $cleaned);
                if ($date && $date->year >= 2020 && $date->year <= 2030) {
                    return $date;
                }
            } catch (\Exception $e) {
            }
        }

        // Try direct parse
        try {
            $date = CarbonImmutable::parse($cleaned);
            if ($date && $date->year >= 2020 && $date->year <= 2030) {
                return $date;
            }
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
                return $src;
            }
        }

        return null;
    }

    private function extractMainImage(DOMXPath $xpath): ?string
    {
        $img = $xpath->query("//img[contains(@class, 'MainImage') or contains(@class, 'ProductImage')]")->item(0);

        if ($img instanceof \DOMElement) {
            $src = $img->getAttribute('src');

            return $src !== '' ? $src : null;
        }

        $fallback = $xpath->query('//img')->item(0);
        if ($fallback instanceof \DOMElement) {
            $src = $fallback->getAttribute('src');

            return $src !== '' ? $src : null;
        }

        return null;
    }

    private function extractImageUrls(DOMXPath $xpath): array
    {
        $urls = [];
        $images = $xpath->query('//img');
        if ($images) {
            foreach ($images as $img) {
                if ($img instanceof \DOMElement) {
                    $src = $img->getAttribute('src');
                    if ($src && ! str_starts_with($src, 'data:')) {
                        $urls[] = $src;
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

    private function cleanText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);

        // Clean multi-byte characters if needed, but keep Japanese
        return trim($text);
    }
}
