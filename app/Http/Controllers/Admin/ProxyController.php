<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProxyStoreRequest;
use App\Models\Proxy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProxyController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.proxies.index', [
            'proxies' => Proxy::query()->latest()->paginate(25),
        ]);
    }

    public function store(ProxyStoreRequest $request): RedirectResponse
    {
        Proxy::query()->create($request->validated());

        return back()->with('success', 'Proxy server added successfully.');
    }

    public function destroy(Proxy $proxy): RedirectResponse
    {
        $proxy->delete();

        return back()->with('success', 'Proxy server removed successfully.');
    }
}
