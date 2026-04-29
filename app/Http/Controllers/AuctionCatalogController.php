<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $auctions = Auction::filter($request->all())
            ->paginate(24)
            ->withQueryString();

        return view('auctions.index', [
            'auctions' => $auctions,
            'filters' => $request->all(),
        ]);
    }
}
