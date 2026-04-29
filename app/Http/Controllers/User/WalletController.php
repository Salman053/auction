<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DepositRequest;
use App\Services\SettingService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Stripe\StripeClient;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');
        $wallet = $user?->wallet;

        $transactions = $wallet
            ? $wallet->transactions()->latest()->paginate(20)
            : collect();

        $multiplierPercent = (int) ($user?->bidding_multiplier_percent ?? 500);
        $depositYen = (int) ($wallet?->balance_yen ?? 0);
        $lockedYen = (int) ($wallet?->locked_balance_yen ?? 0);
        $withdrawalLockedYen = (int) ($wallet?->withdrawal_locked_yen ?? 0);
        $capacityYen = (int) floor($depositYen * ($multiplierPercent / 100));
        $availableCapacityYen = max(0, $capacityYen - $lockedYen - $withdrawalLockedYen);

        return view('user.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'multiplierPercent' => $multiplierPercent,
            'capacityYen' => $capacityYen,
            'availableCapacityYen' => $availableCapacityYen,
        ]);
    }

    public function storeDeposit(DepositRequest $request, WalletService $walletService, SettingService $settingService): RedirectResponse|Redirector|Response
    {
        $user = $request->user('user');

        if ($user === null) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        $validated = $request->validated();
        $provider = (string) $validated['provider'];
        $amountYen = (int) $validated['amount_yen'];
        $transactionId = $validated['transaction_id'] ?? null;
        $receiptPath = null;

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $transaction = $walletService->requestDeposit(
            user: $user,
            amountYen: $amountYen,
            provider: $provider,
            memo: $validated['memo'] ?? null,
            providerReference: $transactionId,
            receiptPath: $receiptPath,
        );

        $stripeEnabled = $settingService->getBool(SettingService::STRIPE_PAYMENT_ENABLED_KEY, false);

        if ($stripeEnabled && $provider === 'stripe') {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));

                $checkoutSession = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'unit_amount' => $amountYen,
                            'product_data' => [
                                'name' => 'WatchHub Wallet Deposit',
                                'description' => 'Increase bidding power',
                            ],
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'customer_email' => $user->email,
                    'metadata' => [
                        'wallet_transaction_id' => $transaction->id,
                    ],
                    'success_url' => route('user.wallet.deposits.stripe.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
                    'cancel_url' => route('user.wallet.deposits.stripe.cancel'),
                ]);

                $transaction->forceFill([
                    'provider_reference' => $checkoutSession->id,
                ])->save();

                return redirect($checkoutSession->url);
            } catch (\Exception $e) {
                // Return back with a friendly error if Stripe fails instead of generating a 500
                return back()->with('error', 'Stripe Payment Gateway unavailable. Please try again later. ('.$e->getMessage().')');
            }
        }

        return back()->with('success', 'Deposit request submitted. An admin will review and approve it.');
    }

    public function stripeSuccess(Request $request, WalletService $walletService): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        if (! $sessionId) {
            return redirect()->route('user.wallet.index')->with('error', 'Invalid payment session.');
        }

        $user = $request->user('user');

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $session = $stripe->checkout->sessions->retrieve((string) $sessionId);

            if ($session->payment_status === 'paid') {
                $transactionId = $session->metadata->wallet_transaction_id ?? null;

                if ($transactionId) {
                    $transaction = $user->wallet->transactions()->where('id', $transactionId)->where('status', 'pending')->first();

                    if ($transaction) {
                        $walletService->approveDeposit($transaction, null, 'Stripe Payment Confirmed');

                        return redirect()->route('user.wallet.index')->with('success', 'Payment successful! Dashboard updated.');
                    }
                }

                return redirect()->route('user.wallet.index')->with('status', 'Payment already processed or transaction invalid.');
            }
        } catch (\Exception $e) {
            return redirect()->route('user.wallet.index')->with('error', 'Could not verify payment: '.$e->getMessage());
        }

        return redirect()->route('user.wallet.index')->with('error', 'Payment incomplete.');
    }

    public function stripeCancel(): RedirectResponse
    {
        return redirect()->route('user.wallet.index')->with('info', 'Payment cancelled.');
    }
}
