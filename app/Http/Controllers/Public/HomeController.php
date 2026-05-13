<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $featured = Auction::query()
            ->whereIn('status', ['active', 'ending_soon'])
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('last_synced_at')
            ->limit(15)
            ->get();

        return view('public.home', compact('featured'));
    }
}
