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
        $auction->increment('view_count');
        $auction->load(['bids' => fn ($query) => $query->latest()->limit(20)]);

        return view('public.auctions.show', [
            'auction' => $auction,
        ]);
    }
}
