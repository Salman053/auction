<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WithdrawalDecisionRequest;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->string('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $query = WithdrawalRequest::query()->with(['user', 'approvedBy'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.withdrawals.index', [
            'withdrawals' => $query->paginate(25)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function decide(
        WithdrawalDecisionRequest $request,
        WithdrawalRequest $withdrawalRequest,
        WalletService $walletService
    ): RedirectResponse {
        $admin = $request->user('admin');
        if ($admin === null) {
            return redirect()->route('admin.login')->with('error', 'Admin login required.');
        }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $walletService->approveWithdrawal($withdrawalRequest, $admin, $validated['memo'] ?? null);
        } else {
            $walletService->rejectWithdrawal($withdrawalRequest, $admin, $validated['memo'] ?? null);
        }

        return back()->with('success', 'Withdrawal status updated successfully.');
    }
}
