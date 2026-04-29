<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScrapingLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScrapingLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.scraping-logs.index', [
            'logs' => ScrapingLog::query()->with('proxy')->latest()->paginate(25),
        ]);
    }
}
