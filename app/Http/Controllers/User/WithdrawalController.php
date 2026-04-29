<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\WithdrawalRequest as WithdrawalFormRequest;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('user');

        $withdrawals = $user
            ? WithdrawalRequest::query()->where('user_id', $user->id)->latest()->paginate(20)
            : collect();

        return view('user.withdrawals.index', [
            'withdrawals' => $withdrawals,
            'wallet' => $user?->wallet,
        ]);
    }

    public function store(WithdrawalFormRequest $request, WalletService $walletService): RedirectResponse
    {
        $user = $request->user('user');
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        $validated = $request->validated();
        $amountYen = (int) $validated['amount_yen'];
        $transactionId = $validated['transaction_id'] ?? null;
        $receiptPath = null;

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $walletService->requestWithdrawal(
            user: $user,
            amountYen: $amountYen,
            destinationType: $validated['destination_type'] ?? null,
            destinationMeta: $validated['destination_meta'] ?? null,
            memo: $validated['memo'] ?? null,
            transactionId: $transactionId,
            receiptPath: $receiptPath,
        );

        return back()->with('success', 'Withdrawal request submitted. Admin will review and process it.');
    }
}
