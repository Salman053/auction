<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BidPlaceRequest;
use App\Jobs\SyncAuctionDetails;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\ShippingRate;
use App\Services\BiddingService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Notifications\UserShipmentConfirmedNotification;
use App\Notifications\UserShipmentRejectedNotification;
use App\Models\AuditLog;
use App\Models\User;
use App\Enums\UserRole;

class AuctionDetailController extends Controller
{
    public function show(Request $request, Auction $auction): View
    {
        // On-demand sync: trigger if stale (more than 15 minutes old)
        if (! $auction->last_synced_at || $auction->last_synced_at->lt(now()->subMinutes(15))) {
            SyncAuctionDetails::dispatchSync($auction);
            $auction->refresh();
        }

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

        $multiplierPercent = (int) ($user?->bidding_multiplier_percent ?? 0);
        if ($multiplierPercent <= 0) {
            $multiplierPercent = app(SettingService::class)->getInt(
                SettingService::DEFAULT_BIDDING_MULTIPLIER_PERCENT_KEY,
                500,
            );
        }

        $capacityYen = (int) floor(((int) ($wallet?->balance_yen ?? 0)) * ($multiplierPercent / 100));
        $availableCapacityYen = max(0, $capacityYen - (int) ($wallet?->locked_balance_yen ?? 0) - (int) ($wallet?->withdrawal_locked_yen ?? 0));

        $similarAuctions = Auction::active()
            ->where('id', '!=', $auction->id)
            ->where('yahoo_category_id', $auction->yahoo_category_id)
            ->where(function ($query) use ($auction) {
                $query->whereBetween('current_bid_yen', [
                    (int) ($auction->current_bid_yen * 0.7),
                    (int) ($auction->current_bid_yen * 1.3),
                ])->orWhere('current_bid_yen', '>=', $auction->current_bid_yen);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();

        if ($similarAuctions->count() < 4) {
            $remaining = 4 - $similarAuctions->count();
            $extraAuctions = Auction::active()
                ->where('id', '!=', $auction->id)
                ->where('yahoo_category_id', $auction->yahoo_category_id)
                ->whereNotIn('id', $similarAuctions->pluck('id'))
                ->inRandomOrder()
                ->limit($remaining)
                ->get();

            $similarAuctions = $similarAuctions->concat($extraAuctions);
        }

        $watchlistedAuctionIds = $user
            ? $user->watchlistItems()->pluck('auction_id')->all()
            : [];

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
            'capacityYen' => $capacityYen,
            'availableCapacityYen' => $availableCapacityYen,
            'categories' => Category::where('depth', 0)->orderBy('priority', 'desc')->orderBy('name')->limit(8)->get(),
            'similarAuctions' => $similarAuctions,
            'watchlistedAuctionIds' => $watchlistedAuctionIds,
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

        return back()->with('success', 'Bid Placed Successfully on '.$auction->title.' with amount '.$validated['amount_yen']);
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

        AuditLog::create([
            'user_id' => $user->id,
            'log_name' => 'shipment',
            'description' => "User {$user->name} (#{$user->id}) confirmed shipment for auction #{$auction->id} (Yahoo ID: {$auction->yahoo_auction_id}).",
            'subject_id' => $auction->id,
            'subject_type' => Auction::class,
            'properties' => ['old_status' => 'pending', 'new_status' => 'bidder_confirmed'],
        ]);

        // Notify admins
        $admins = User::where('role', UserRole::Admin->value)->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new UserShipmentConfirmedNotification($auction, $user));
            } catch (\Exception $e) {
                \Log::error("Failed to notify admin {$admin->id} about user shipment confirmation for auction {$auction->id}: ".$e->getMessage());
            }
        }

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

        AuditLog::create([
            'user_id' => $user->id,
            'log_name' => 'shipment',
            'description' => "User {$user->name} (#{$user->id}) rejected shipment for auction #{$auction->id} (Yahoo ID: {$auction->yahoo_auction_id}).",
            'subject_id' => $auction->id,
            'subject_type' => Auction::class,
            'properties' => ['old_status' => 'pending', 'new_status' => 'bidder_rejected'],
        ]);

        // Notify admins
        $admins = User::where('role', UserRole::Admin->value)->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new UserShipmentRejectedNotification($auction, $user));
            } catch (\Exception $e) {
                \Log::error("Failed to notify admin {$admin->id} about user shipment rejection for auction {$auction->id}: ".$e->getMessage());
            }
        }

        return back()->with('success', 'Shipment request has been rejected.');
    }

    public function getUpdates(Request $request, Auction $auction): JsonResponse
    {
        // Trigger background sync if active and stale (more than 10 seconds since last sync)
        if ($auction->status === 'active' && (! $auction->last_synced_at || $auction->last_synced_at->lt(now()->subSeconds(10)))) {
            SyncAuctionDetails::dispatch($auction)->onQueue('high');
        }

        $auction->load(['bids' => fn ($query) => $query->with('user')->latest()->limit(30)]);

        $highestActiveBid = Bid::where('auction_id', $auction->id)
            ->where('status', 'active')
            ->orderByDesc('max_amount_yen')
            ->orderBy('created_at')
            ->first();

        $user = $request->user('user');
        $userHighestActiveBid = $user
            ? Bid::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->orderByDesc('max_amount_yen')
                ->orderBy('created_at')
                ->first()
            : null;

        $isTopBidder = $userHighestActiveBid && $highestActiveBid && $userHighestActiveBid->id === $highestActiveBid->id;

        return response()->json([
            'current_bid_yen' => (int) $auction->current_bid_yen,
            'bid_count' => (int) $auction->bid_count,
            'highest_active_bid_id' => $highestActiveBid?->id,
            'ends_at' => $auction->ends_at?->toISOString(),
            'ends_at_human' => $auction->ends_at?->diffForHumans(),
            'bids_html' => view('user.auctions._bid_history', ['bids' => $auction->bids])->render(),
            'user_bid_status' => [
                'has_bid' => $userHighestActiveBid !== null,
                'is_top_bidder' => $isTopBidder,
                'user_max_bid' => $userHighestActiveBid ? (int) $userHighestActiveBid->max_amount_yen : 0,
            ],
        ]);
    }
}
