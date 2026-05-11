<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\WatchlistItem;
use App\Notifications\WatchlistAddedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');

        $items = $user
            ? $user->watchlistItems()->with('auction')->latest()->paginate(24)
            : collect();

        return view('user.watchlist.index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request, Auction $auction): RedirectResponse
    {
        $user = $request->user('user');
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Please login to manage your watchlist.');
        }

        $item = WatchlistItem::query()->firstOrCreate([
            'user_id' => $user->id,
            'auction_id' => $auction->id,
        ]);

        if ($item->wasRecentlyCreated) {
            $user->notify(new WatchlistAddedNotification($auction));
        }

        return back()->with('success', 'Item successfully added to your watchlist.');
    }

    public function destroy(Request $request, Auction $auction): RedirectResponse
    {
        $user = $request->user('user');
        if ($user === null) {
            return redirect()->route('login');
        }

        WatchlistItem::query()
            ->where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->delete();

        return back()->with('success', 'Item removed from your watchlist.');
    }
}
    