<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function about(Request $request): View
    {
        return view('public.pages.about');
    }

    public function howItWorks(Request $request): View
    {
        return view('public.pages.how-it-works');
    }

    public function faq(Request $request): View
    {
        return view('public.pages.faq');
    }
}
