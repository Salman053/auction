<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionController extends Controller
{
    public function show(Request $request, Auction $auction): View
    {
        // On-demand sync: trigger if stale (more than 15 minutes old)
        if (! $auction->last_synced_at || $auction->last_synced_at->lt(now()->subMinutes(15))) {
            \App\Jobs\SyncAuctionDetails::dispatch($auction)->onQueue('high');
        }

        $auction->increment('view_count');
        $auction->load(['bids' => fn ($query) => $query->latest()->limit(20)]);

        return view('public.auctions.show', [
            'auction' => $auction,
        ]);
    }
}
