<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Services\BiddingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BidController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');
        if (!$user) {
            return view('user.bids.index', ['bids' => collect(), 'counts' => []]);
        }

        $status = $request->query('status', 'all');
        
        $query = $user->bids()->with('auction');

        // Get counts for tabs
        $allCount = (clone $query)->count();
        $wonCount = (clone $query)->where('status', 'won')->count();
        $lostCount = (clone $query)->whereIn('status', ['outbid', 'lost'])->count();

        if ($status === 'won') {
            $query->where('status', 'won');
        } elseif ($status === 'lost') {
            $query->whereIn('status', ['outbid', 'lost']);
        }

        $bids = $query->latest()->paginate(24)->withQueryString();

        return view('user.bids.index', [
            'bids' => $bids,
            'currentStatus' => $status,
            'counts' => [
                'all' => $allCount,
                'won' => $wonCount,
                'lost' => $lostCount,
            ],
        ]);
    }

    public function cancel(Request $request, Bid $bid, BiddingService $biddingService): RedirectResponse
    {
        $user = $request->user('user');
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        try {
            $biddingService->cancelBid($user, $bid);

            return back()->with('success', 'Bid cancelled successfully.');
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to cancel bid.';

            return back()->with('error', $message);
        } catch (\Throwable $exception) {
            Log::error('Bid cancellation failed.', [
                'user_id' => $user->id,
                'bid_id' => $bid->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Unable to cancel bid at this time. Please try again later.');
        }
    }
}
