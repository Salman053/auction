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
        $filters = $request->all();
        if (! isset($filters['status'])) {
            $filters['status'] = 'active';
        }

        $auctions = Auction::filter($filters)
            ->withCount('watchlistItems')
            ->paginate(24)
            ->withQueryString();

        $user = $request->user('user');
        $watchlistedAuctionIds = $user
            ? $user->watchlistItems()->pluck('auction_id')->all()
            : [];

        return view('user.auctions.index', [
            'auctions' => $auctions,
            'filters' => $filters,
            'watchlistedAuctionIds' => $watchlistedAuctionIds,
        ]);
    }
}
