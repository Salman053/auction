<?php

namespace App\Services;

use App\Models\Proxy;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ];

    private int $delay;

    private int $maxRetries;

    private bool $debug;

    private bool $verifySsl;

    public function __construct(private YahooAuctionHtmlParser $parser)
    {
        $this->debug = (bool) config('scraper.debug', false);
        $this->verifySsl = (bool) config('scraper.verify_ssl', true);
        $this->delay = (int) config('scraper.delay', 2);
        $this->maxRetries = (int) config('scraper.max_retries', 3);
    }

    public function search(string $keyword = '', int $page = 1, ?string $categoryId = null, bool $newOnly = false, ?int $minPrice = null, ?int $maxPrice = null): array
    {
        $encodedKeyword = urlencode($keyword);
        $offset = (($page - 1) * 50) + 1;
        $url = "https://auctions.yahoo.co.jp/search/search?p={$encodedKeyword}&b={$offset}";

        if ($categoryId) {
            $url .= "&auccat={$categoryId}";
        }

        if ($newOnly) {
            $url .= '&new=1';
        }

        if ($minPrice !== null) {
            $url .= "&aucminprice={$minPrice}";
        }

        if ($maxPrice !== null) {
            $url .= "&aucmaxprice={$maxPrice}";
        }

        if ($this->debug) {
            Log::debug('Fetching URL for Yahoo search', ['url' => $url]);
        }

        $html = $this->fetch($url);

        if (! $html) {
            Log::warning('Failed to fetch search results', ['keyword' => $keyword, 'page' => $page]);

            return [];
        }

        if ($this->debug) {
            Log::debug('HTML fetched for Yahoo search', ['length' => strlen($html)]);
        }

        $results = $this->parser->parseSearchResults($html);

        if ($this->debug) {
            Log::debug('Parser returned Yahoo search results', ['count' => count($results)]);
        }

        return $results;
    }

    public function getAuctionDetails(string $auctionId): array
    {
        $url = "https://page.auctions.yahoo.co.jp/jp/auction/{$auctionId}";
        $html = $this->fetch($url);

        if (! $html) {
            return [];
        }

        if ($this->debug) {
            $this->saveLastHtml($html, "auction_{$auctionId}");
        }

        return $this->parser->parseAuctionDetailHtml($html, $url);
    }

    private function saveLastHtml(string $html, string $suffix): void
    {
        try {
            $path = storage_path("logs/last_scrape_{$suffix}.html");
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $html);
        } catch (\Exception $e) {
            Log::error('Failed to save debug HTML', ['error' => $e->getMessage()]);
        }
    }

    public function getHtml(string $url): ?string
    {
        return $this->fetch($url);
    }

    private function fetch(string $url): ?string
    {
        $attempt = 0;
        $response = null;

        while ($attempt < $this->maxRetries) {
            $ch = curl_init();
            $userAgent = $this->userAgents[array_rand($this->userAgents)];

            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: ja-JP,ja;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: max-age=0',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
                CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_ENCODING => '',
                CURLOPT_COOKIEJAR => storage_path('app/cookies.txt'),
                CURLOPT_COOKIEFILE => storage_path('app/cookies.txt'),
            ]);

            // Proxy rotation
            $proxy = Proxy::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('disabled_until')
                        ->orWhere('disabled_until', '<=', now());
                })
                ->inRandomOrder()
                ->first();

            if ($proxy) {
                $proxyUrl = "{$proxy->scheme}://{$proxy->host}:{$proxy->port}";
                curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
                if ($proxy->username && $proxy->password) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$proxy->username}:{$proxy->password}");
                }
            }

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($response !== false && $httpCode === 200) {
                if ($proxy) {
                    $proxy->increment('success_count');
                    $proxy->update([
                        'last_used_at' => now(),
                        'avg_response_ms' => ($proxy->avg_response_ms > 0) ? (int) (($proxy->avg_response_ms + $durationMs) / 2) : $durationMs,
                    ]);
                }
                curl_close($ch);
                break;
            }

            $error = curl_error($ch) ?: "HTTP {$httpCode}";
            $attempt++;

            if ($proxy) {
                $proxy->increment('failure_count');
                $updateData = [
                    'last_error' => $error,
                    'last_used_at' => now(),
                ];

                // If blocked or frequent failure, disable for a while
                if ($httpCode === 403 || $httpCode === 429) {
                    $updateData['disabled_until'] = now()->addMinutes(30);
                    Log::warning("Proxy {$proxy->host} blocked (HTTP {$httpCode}). Disabling for 30 mins.");
                }

                $proxy->update($updateData);
            }

            if ($this->debug) {
                Log::debug('Yahoo scraper request failed', [
                    'attempt' => $attempt,
                    'url' => $url,
                    'error' => $error,
                    'proxy' => $proxy?->host,
                ]);
            }

            curl_close($ch);

            if ($attempt >= $this->maxRetries) {
                return null;
            }

            sleep($this->delay * $attempt);
        }

        return $response;
    }
}
