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
            ->latest('ends_at')
            ->limit(8)
            ->get();

        return view('public.home', [
            'featured' => $featured,
        ]);
    }
}
