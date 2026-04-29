<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_auctions' => Auction::where('status', 'active')->count(),
            'total_bids' => Bid::count(),
            'total_wallet_balance' => Wallet::sum('balance_yen'),
            'recent_bids' => Bid::with(['user', 'auction'])->latest()->limit(10)->get(),
        ];

        return view('admin.analytics.index', $stats);
    }
}
