<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use App\Models\ShippingRate;
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
            ->inRandomOrder()
            ->limit(15)
            ->get();

        $carouselAuctions = $featured->take(5);
        $secondaryCarouselAuctions = $featured->slice(8);

        $categories = Category::where('depth', 0)
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->limit(7)
            ->get();

        $navCategories = Category::where('depth', 0)
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $shippingLocations = ShippingRate::query()
            ->orderBy('country')
            ->orderBy('name')
            ->get();

        return view('public.home', compact('featured', 'carouselAuctions', 'secondaryCarouselAuctions', 'categories', 'navCategories', 'shippingLocations'));
    }
}
