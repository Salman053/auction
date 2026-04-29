<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'all');
        $query = Auction::withCount(['bids', 'watchlistItems']);

        if ($tab === 'won') {
            $query->where(function ($q) {
                $q->where('status', 'ended')
                    ->orWhere('ends_at', '<=', now());
            })->where('bid_count', '>', 0);
        } elseif ($tab === 'shipment_pending') {
            $query->where('shipment_status', 'bidder_confirmed');
        } elseif ($tab === 'active') {
            $query->where('status', 'active')
                ->where('ends_at', '>', now());
        }

        $auctions = $query->filter($request->all())
            ->paginate(25)
            ->withQueryString();

        $topBids = Auction::where('bid_count', '>', 0)
            ->orderBy('bid_count', 'desc')
            ->limit(3)
            ->get();

        return view('admin.auctions.index', [
            'auctions' => $auctions,
            'filters' => $request->all(),
            'currentTab' => $tab,
            'topBids' => $topBids,
        ]);
    }

    public function show(Auction $auction): View
    {
        $auction->load([
            'bids' => fn ($q) => $q->with('user')->latest(),
            'watchlistItems' => fn ($q) => $q->with('user')->latest(),
        ]);

        $stats = [
            'total_bids' => $auction->bids->count(),
            'unique_bidders' => $auction->bids->pluck('user_id')->unique()->count(),
            'highest_bid' => $auction->bids->max('amount_yen') ?? 0,
            'watchers' => $auction->watchlistItems->count(),
        ];

        return view('admin.auctions.show', [
            'auction' => $auction,
            'stats' => $stats,
        ]);
    }

    public function approveShipment(Auction $auction, Request $request): RedirectResponse
    {
        if ($auction->shipment_status !== 'bidder_confirmed') {
            return back()->with('error', 'Shipment must be confirmed by the bidder first.');
        }

        $auction->update([
            'shipment_status' => 'admin_approved',
            'admin_approved_at' => now(),
        ]);

        return back()->with('success', 'Shipment approved successfully!');
    }

    public function rejectShipment(Auction $auction, Request $request): RedirectResponse
    {
        if ($auction->shipment_status !== 'bidder_confirmed') {
            return back()->with('error', 'Shipment must be confirmed by the bidder first.');
        }

        $auction->update([
            'shipment_status' => 'pending',
            'bidder_confirmed_at' => null,
        ]);

        return back()->with('success', 'Shipment rejected and returned to pending state.');
    }
}
