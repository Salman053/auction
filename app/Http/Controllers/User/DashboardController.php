<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\WalletTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');
        $wallet = $user?->wallet;
        $multiplierPercent = (int) ($user?->bidding_multiplier_percent ?? 500);

        $depositYen = (int) ($wallet?->balance_yen ?? 0);
        $lockedYen = (int) ($wallet?->locked_balance_yen ?? 0);
        $withdrawalLockedYen = (int) ($wallet?->withdrawal_locked_yen ?? 0);
        $capacityYen = (int) floor($depositYen * ($multiplierPercent / 100));
        $availableCapacityYen = max(0, $capacityYen - $lockedYen - $withdrawalLockedYen);

        $activeBidsCount = $user
            ? Bid::query()->where('user_id', $user->id)->where('status', 'active')->count()
            : 0;

        $wonAuctionsCount = $user
            ? Auction::query()->where('winner_user_id', $user->id)->count()
            : 0;

        $unreadNotificationCount = $user ? $user->unreadNotifications()->count() : 0;

        $start = CarbonImmutable::now()->subDays(6)->startOfDay();
        $end = CarbonImmutable::now()->endOfDay();

        $labels = [];
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->addDays($i);
            $key = $day->format('Y-m-d');
            $days[] = $key;
            $labels[] = $day->format('D');
        }

        $bidsByDay = array_fill_keys($days, 0);
        if ($user) {
            $userBids = Bid::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->get(['created_at']);

            foreach ($userBids as $bid) {
                $key = $bid->created_at?->format('Y-m-d');
                if ($key && array_key_exists($key, $bidsByDay)) {
                    $bidsByDay[$key]++;
                }
            }
        }

        $netWalletByDay = array_fill_keys($days, 0);
        if ($wallet) {
            $walletTransactions = WalletTransaction::query()
                ->where('wallet_id', $wallet->id)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$start, $end])
                ->get(['created_at', 'amount_yen']);

            foreach ($walletTransactions as $tx) {
                $key = $tx->created_at?->format('Y-m-d');
                if ($key && array_key_exists($key, $netWalletByDay)) {
                    $netWalletByDay[$key] += (int) $tx->amount_yen;
                }
            }
        }

        $recentWins = $user
            ? Auction::query()
                ->where('winner_user_id', $user->id)
                ->latest('updated_at')
                ->limit(5)
                ->get()
            : collect();

        return view('user.dashboard', [
            'user' => $user,
            'wallet' => $wallet,
            'multiplierPercent' => $multiplierPercent,
            'capacityYen' => $capacityYen,
            'availableCapacityYen' => $availableCapacityYen,
            'activeBidsCount' => $activeBidsCount,
            'wonAuctionsCount' => $wonAuctionsCount,
            'unreadNotificationCount' => $unreadNotificationCount,
            'recentWins' => $recentWins,
            'chartData' => [
                'labels' => $labels,
                'bids' => array_values($bidsByDay),
                'walletNet' => array_values($netWalletByDay),
            ],
        ]);
    }
}
