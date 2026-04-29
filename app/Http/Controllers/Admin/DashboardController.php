<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuditLog;
use App\Models\Proxy;
use App\Models\ScrapingLog;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $now = CarbonImmutable::now();

        $recentLogs = ScrapingLog::query()
            ->whereNotNull('started_at')
            ->where('started_at', '>=', $now->subDay())
            ->orderBy('started_at')
            ->get(['status', 'started_at']);

        $bucketHours = [0, 4, 8, 12, 16, 20];
        $labels = [];
        $scrapes = [];
        $successRates = [];

        foreach ($bucketHours as $hour) {
            $bucketStart = $now->startOfDay()->addHours($hour);
            $bucketEnd = $bucketStart->addHours(4);

            $bucket = $recentLogs->filter(function (ScrapingLog $log) use ($bucketStart, $bucketEnd): bool {
                return $log->started_at !== null
                    && $log->started_at->greaterThanOrEqualTo($bucketStart)
                    && $log->started_at->lessThan($bucketEnd);
            });

            $total = $bucket->count();
            $completed = $bucket->where('status', 'completed')->count();

            $labels[] = $bucketStart->format('H:i');
            $scrapes[] = $total;
            $successRates[] = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        }

        $lastRun = ScrapingLog::query()->latest('started_at')->first();
        $scraperStatus = $lastRun?->status ?? 'unknown';

        $failedProxyCount = Proxy::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNotNull('disabled_until')
                    ->where('disabled_until', '>', now());
            })
            ->count();

        $pendingDepositCount = WalletTransaction::query()
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->count();

        $approvedDepositSumYen = (int) WalletTransaction::query()
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount_yen');

        $recentActivity = AuditLog::query()
            ->with('actor')
            ->latest('created_at')
            ->limit(3)
            ->get();

        return view('admin.dashboard', [
            'userCount' => User::query()->where('role', 'user')->count(),
            'adminCount' => User::query()->where('role', 'admin')->count(),
            'auctionCount' => Auction::query()->count(),
            'activeProxyCount' => Proxy::query()->where('is_active', true)->count(),
            'failedProxyCount' => $failedProxyCount,
            'pendingDepositCount' => $pendingDepositCount,
            'approvedDepositSumYen' => $approvedDepositSumYen,
            'scraperStatus' => $scraperStatus,
            'recentActivity' => $recentActivity,
            'performanceData' => [
                'labels' => $labels,
                'scrapes' => $scrapes,
                'success_rate' => $successRates,
            ],
        ]);
    }
}
