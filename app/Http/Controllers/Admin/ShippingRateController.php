<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShippingRate;
use App\Http\Requests\Admin\ShippingRateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController
{
    /**
     * Show a list of shipping rates.
     */
    public function index(): View
    {
        $rates = ShippingRate::orderBy('country', 'asc')
            ->orderBy('port', 'asc')
            ->paginate(15);
        return view('admin.shipping_rates.index', compact('rates'));
    }

    /**
     * Show the form for creating a new shipping rate.
     */
    public function create(): View
    {
        return view('admin.shipping_rates.create');
    }

    /**
     * Store a newly created shipping rate.
     */
    public function store(ShippingRateRequest $request): RedirectResponse
    {
        ShippingRate::create($request->validated());
        return redirect()
            ->route('admin.shipping_rates.index')
            ->with('status', 'Shipping rate created successfully');
    }

    /**
     * Show the edit form for a shipping rate.
     */
    public function edit(ShippingRate $shippingRate): View
    {
        return view('admin.shipping_rates.edit', compact('shippingRate'));
    }

    /**
     * Update a shipping rate.
     */
    public function update(ShippingRateRequest $request, ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->update($request->validated());
        return redirect()
            ->route('admin.shipping_rates.index')
            ->with('status', 'Shipping rate updated successfully');
    }

    /**
     * Delete a shipping rate.
     */
    public function destroy(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->delete();
        return redirect()
            ->route('admin.shipping_rates.index')
            ->with('status', 'Shipping rate deleted successfully');
    }
}
?>
