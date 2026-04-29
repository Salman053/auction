<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionController extends Controller
{
    public function index(Request $request): View
    {
        $auctions = Auction::filter($request->all())
            ->paginate(24)
            ->withQueryString();

        $user = $request->user('user');
        $watchlistedAuctionIds = $user
            ? $user->watchlistItems()->pluck('auction_id')->all()
            : [];

        return view('user.auctions.index', [
            'auctions' => $auctions,
            'filters' => $request->all(),
            'watchlistedAuctionIds' => $watchlistedAuctionIds,
        ]);
    }
}
