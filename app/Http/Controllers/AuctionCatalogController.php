<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->all();
        if (! isset($filters['status'])) {
            $filters['status'] = 'active';
        }

        $auctions = Auction::filter($filters)
            ->paginate(24)
            ->withQueryString();

        return view('auctions.index', [
            'auctions' => $auctions,
            'filters' => $filters,
        ]);
    }
}
