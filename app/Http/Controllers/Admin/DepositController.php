<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepositDecisionRequest;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->string('status', 'pending');

        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $query = WalletTransaction::query()
            ->with(['wallet.user', 'approvedBy'])
            ->where('type', 'deposit')
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.deposits.index', [
            'transactions' => $query->paginate(25)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function decide(
        DepositDecisionRequest $request,
        WalletTransaction $transaction,
        WalletService $walletService
    ): RedirectResponse {
        $admin = $request->user('admin');

        if ($admin === null) {
            return redirect()->route('admin.login')->with('error', 'Admin login required.');
        }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $walletService->approveDeposit($transaction, $admin, $validated['memo'] ?? null);
        } else {
            $walletService->rejectDeposit($transaction, $admin, $validated['memo'] ?? null);
        }

        return back()->with('success', 'Deposit status updated successfully.');
    }
}
