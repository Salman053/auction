<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncAuctionDetails;
use App\Models\Auction;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\AdminShipmentApprovedNotification;
use App\Notifications\AdminShipmentRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuctionController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'active');
        $query = Auction::withCount(['bids', 'watchlistItems']);

        if ($tab === 'won') {
            $query->where(function ($q) {
                $q->where('status', 'ended')
                    ->orWhere('ends_at', '<=', now());
            })->where('bid_count', '>', 0);
        } elseif ($tab === 'shipment_pending') {
            $query->where('shipment_status', 'bidder_confirmed');
        } elseif ($tab === 'active') {
            $query->active();
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
        // On-demand sync: trigger if stale (more than 15 minutes old)
        if (! $auction->last_synced_at || $auction->last_synced_at->lt(now()->subMinutes(15))) {
            SyncAuctionDetails::dispatchSync($auction);
            $auction->refresh();
        }

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

        AuditLog::create([
            'actor_user_id' => $request->user('admin')->id,
            'guard' => 'admin',
            'event' => 'shipment_approved',
            'meta' => [
                'description' => "Admin {$request->user('admin')->name} (#{$request->user('admin')->id}) approved shipment for auction #{$auction->id} (Yahoo ID: {$auction->yahoo_auction_id}).",
                'subject_id' => $auction->id,
                'subject_type' => Auction::class,
                'old_status' => 'bidder_confirmed',
                'new_status' => 'admin_approved',
            ],
        ]);

        // Notify winner
        if ($auction->winner_user_id) {
            $winner = User::find($auction->winner_user_id);
            if ($winner) {
                try {
                    $winner->notify(new AdminShipmentApprovedNotification($auction));
                } catch (\Exception $e) {
                    Log::error("Failed to notify winner {$winner->id} about admin shipment approval for auction {$auction->id}: ".$e->getMessage());
                }
            }
        }

        return back()->with('success', 'Shipment approved successfully!');
    }

    public function rejectShipment(Auction $auction, Request $request): RedirectResponse
    {
        if ($auction->shipment_status !== 'bidder_confirmed' && $auction->shipment_status !== 'bidder_rejected') {
            return back()->with('error', 'Shipment must be confirmed or rejected by the bidder first.');
        }

        $oldStatus = $auction->shipment_status;
        $auction->update([
            'shipment_status' => 'pending',
            'bidder_confirmed_at' => null,
        ]);

        AuditLog::create([
            'actor_user_id' => $request->user('admin')->id,
            'guard' => 'admin',
            'event' => 'shipment_reset',
            'meta' => [
                'description' => "Admin {$request->user('admin')->name} (#{$request->user('admin')->id}) reset shipment for auction #{$auction->id} (Yahoo ID: {$auction->yahoo_auction_id}) to pending.",
                'subject_id' => $auction->id,
                'subject_type' => Auction::class,
                'old_status' => $oldStatus,
                'new_status' => 'pending',
            ],
        ]);

        // Notify winner
        if ($auction->winner_user_id) {
            $winner = User::find($auction->winner_user_id);
            if ($winner) {
                try {
                    $winner->notify(new AdminShipmentRejectedNotification($auction));
                } catch (\Exception $e) {
                    Log::error("Failed to notify winner {$winner->id} about admin shipment rejection for auction {$auction->id}: ".$e->getMessage());
                }
            }
        }

        return back()->with('success', 'Shipment state has been reset to pending.');
    }

    public function sync(Auction $auction): RedirectResponse
    {
        SyncAuctionDetails::dispatchSync($auction);

        return back()->with('success', 'Auction successfully synced with Yahoo Auctions!');
    }

    public function export(Request $request): StreamedResponse
    {
        $tab = $request->query('tab', 'active');
        $query = Auction::query();

        if ($tab === 'won') {
            $query->where(function ($q) {
                $q->where('status', 'ended')
                    ->orWhere('ends_at', '<=', now());
            })->where('bid_count', '>', 0);
        } elseif ($tab === 'shipment_pending') {
            $query->where('shipment_status', 'bidder_confirmed');
        } elseif ($tab === 'active') {
            $query->active();
        }

        $auctions = $query->filter($request->all())->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=auctions_export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($auctions) {
            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF"); // BOM for Excel UTF-8

            fputcsv($file, [
                'ID',
                'Yahoo Auction ID',
                'Category ID',
                'Title',
                'Condition',
                'Starting Bid (Yen)',
                'Current Bid (Yen)',
                'Bids Count',
                'Status',
                'Starts At',
                'Ends At',
                'Seller Name',
                'Yahoo Seller ID',
                'Seller Rating',
                'Thumbnail URL',
                'View Count',
                'Shipment Status',
                'Winner User ID',
                'Yahoo Watcher Count',
                'Created At',
            ]);

            foreach ($auctions as $auction) {
                fputcsv($file, [
                    $auction->id,
                    $auction->yahoo_auction_id,
                    $auction->yahoo_category_id,
                    $auction->title,
                    $auction->condition,
                    $auction->starting_bid_yen,
                    $auction->current_bid_yen,
                    $auction->bid_count,
                    $auction->status,
                    $auction->starts_at?->toDateTimeString(),
                    $auction->ends_at?->toDateTimeString(),
                    $auction->seller_name,
                    $auction->yahoo_seller_id,
                    $auction->seller_rating,
                    $auction->thumbnail_url,
                    $auction->view_count,
                    $auction->shipment_status,
                    $auction->winner_user_id,
                    $auction->yahoo_watcher_count,
                    $auction->created_at?->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
