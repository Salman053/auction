<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BidPlaceRequest;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\ShippingRate;
use App\Services\BiddingService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionDetailController extends Controller
{
    public function show(Request $request, Auction $auction): View
    {
        $auction->increment('view_count');
        $auction->loadCount('watchlistItems');
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

        $isWatched = $user
            ? $user->watchlistItems()->where('auction_id', $auction->id)->exists()
            : false;

        $multiplierPercent = (int) ($user->bidding_multiplier_percent ?? 0);
        if ($multiplierPercent <= 0) {
            $multiplierPercent = app(SettingService::class)->getInt(
                SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY,
                500,
            );
        }

        $capacityYen = (int) floor(((int) ($wallet?->balance_yen ?? 0)) * ($multiplierPercent / 100));
        $availableCapacityYen = max(0, $capacityYen - (int) ($wallet?->locked_balance_yen ?? 0) - (int) ($wallet?->withdrawal_locked_yen ?? 0));

        return view('user.auctions.show', [
            'auction' => $auction,
            'wallet' => $wallet,
            'shippingRates' => ShippingRate::all(),
            'userShippingRate' => $user?->shippingRate,
            'highestActiveBid' => $highestActiveBid,
            'userHighestActiveBid' => $userHighestActiveBid,
            'canBid' => $canBid,
            'isWatched' => $isWatched,
            'multiplierPercent' => $multiplierPercent,
            'availableCapacityYen' => $availableCapacityYen,
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

    public function rejectShipment(Auction $auction, Request $request): RedirectResponse
    {
        $user = $request->user('user');
        if ($auction->winner_user_id !== $user->id) {
            abort(403);
        }

        if ($auction->shipment_status !== 'pending') {
            return back()->with('error', 'Shipment cannot be rejected at this stage.');
        }

        $auction->update([
            'shipment_status' => 'bidder_rejected',
            'bidder_confirmed_at' => null,
        ]);

        return back()->with('success', 'Shipment request has been rejected.');
    }

    public function getUpdates(Auction $auction): JsonResponse
    {
        $auction->load(['bids' => fn ($query) => $query->with('user')->latest()->limit(30)]);

        $highestActiveBid = Bid::where('auction_id', $auction->id)
            ->where('status', 'active')
            ->orderByDesc('max_amount_yen')
            ->orderBy('created_at')
            ->first();

        return response()->json([
            'current_bid_yen' => (int) $auction->current_bid_yen,
            'bid_count' => (int) $auction->bid_count,
            'highest_active_bid_id' => $highestActiveBid?->id,
            'ends_at_human' => $auction->ends_at?->diffForHumans(),
            'bids_html' => view('user.auctions._bid_history', ['bids' => $auction->bids])->render(),
        ]);
    }
}
