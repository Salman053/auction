<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BidPlaceRequest;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\ShippingRate;
use App\Services\BiddingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionDetailController extends Controller
{
    public function show(Request $request, Auction $auction): View
    {
        $auction->increment('view_count');
        $auction->load(['bids' => fn ($query) => $query->with('user')->latest()->limit(30)]);

        $user = $request->user('user');
        $wallet = $user?->wallet;

        $highestActiveBid = Bid::where('auction_id', $auction->id)
            ->where('status', 'active')
            ->orderByDesc('max_amount_yen')
            ->orderBy('created_at')
            ->first();

        $userHighestActiveBid = $user
            ? Bid::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->orderByDesc('max_amount_yen')
                ->orderBy('created_at')
                ->first()
            : null;

        $canBid = in_array($auction->status, ['active', 'ending_soon'], true)
            && ($auction->ends_at === null || $auction->ends_at->isFuture());

        return view('user.auctions.show', [
            'auction' => $auction,
            'wallet' => $wallet,
            'shippingRates' => ShippingRate::all(),
            'userShippingRate' => $user?->shippingRate,
            'highestActiveBid' => $highestActiveBid,
            'userHighestActiveBid' => $userHighestActiveBid,
            'canBid' => $canBid,
        ]);
    }

    public function storeBid(
        BidPlaceRequest $request,
        Auction $auction,
        BiddingService $biddingService
    ): RedirectResponse {
        $user = $request->user('user');
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Please login to place a bid.');
        }

        $validated = $request->validated();
        $shippingRateId = (int) $request->input('shipping_rate_id');

        $biddingService->placeBid($user, $auction, (int) $validated['amount_yen'], $shippingRateId);

        return back()->with('status', 'bid-placed');
    }

    public function confirmShipment(Auction $auction, Request $request): RedirectResponse
    {
        $user = $request->user('user');
        if ($auction->winner_user_id !== $user->id) {
            abort(403);
        }

        if ($auction->shipment_status !== 'pending') {
            return back()->with('error', 'Shipment is already confirmed or processed.');
        }

        $auction->update([
            'shipment_status' => 'bidder_confirmed',
            'bidder_confirmed_at' => now(),
        ]);

        return back()->with('success', 'Shipment details confirmed! Waiting for admin approval.');
    }
}
