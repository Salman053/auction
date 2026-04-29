<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeConnectController extends Controller
{
    /**
     * Initiate the Stripe Connect onboarding flow.
     */
    public function connect(Request $request): RedirectResponse
    {
        $user = $request->user('user');
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            // Re-use existing account or create a new Express account
            if (empty($user->stripe_account_id)) {
                $account = $stripe->accounts->create([
                    'type' => 'express',
                    'email' => $user->email,
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                ]);

                // Save the un-onboarded Stripe ID to the user immediately
                $user->forceFill([
                    'stripe_account_id' => $account->id,
                ])->save();
            }

            // Generate an onboarding link tied to this specific account ID
            $accountLink = $stripe->accountLinks->create([
                'account' => $user->stripe_account_id,
                'refresh_url' => route('user.profile.stripe.refresh'),
                'return_url' => route('user.profile.stripe.return'),
                'type' => 'account_onboarding',
            ]);

            return redirect($accountLink->url);
        } catch (ApiErrorException $e) {
            return back()->with('error', 'Stripe connection failed: '.$e->getMessage());
        }
    }

    /**
     * Return callback from Stripe when user successfully completes onboarding.
     */
    public function returnUrl(Request $request): RedirectResponse
    {
        return redirect()->route('user.profile.edit')->with('success', 'Stripe account connected successfully. You are now ready to receive automatic payouts!');
    }

    /**
     * Refresh callback if the Stripe session expires.
     */
    public function refreshUrl(Request $request): RedirectResponse
    {
        // Just redirect back to the connect method to generate a new fresh link.
        return redirect()->route('user.profile.stripe.connect');
    }
}
