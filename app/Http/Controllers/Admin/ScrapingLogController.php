<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ScrapeAllYahooJob;
use App\Models\ScrapingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ScrapingLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.scraping-logs.index', [
            'logs' => ScrapingLog::query()->with('proxy')->latest()->paginate(25),
        ]);
    }

    public function startScrape(Request $request)
    {
        if (ScrapingLog::query()->where('status', 'running')->exists()) {
            return back()->with('error', 'A scrape job is already running. Please wait for it to complete.');
        }

        Cache::forget('scraper:stop_requested');
        Cache::forget('scraper:running');

        ScrapeAllYahooJob::dispatch(
            pages: 13,
            scrapeDelay: 1,
            min: 0,
            max: 200,
            fetchDetails: true
        );

        return back()->with('success', 'Scraping job dispatched to background.');
    }

    public function stopScrape(Request $request)
    {
        Cache::put('scraper:stop_requested', true, now()->addHour());

        ScrapingLog::query()
            ->where('status', 'running')
            ->update([
                'status' => 'stopped',
                'ended_at' => now(),
                'error_message' => 'Manually stopped by admin.',
            ]);

        return back()->with('success', 'Stop signal sent. The scraper will halt after the current page.');
    }
}
