<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Notifications\AdminNewDepositRequestNotification;
use App\Notifications\DepositApprovedNotification;
use App\Notifications\WithdrawalProcessedNotification;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function requestDeposit(User $user, int $amountYen, string $provider, ?string $memo = null, ?string $providerReference = null, ?string $receiptPath = null): WalletTransaction
    {
        $wallet = $user->wallet;

        if ($wallet === null) {
            $wallet = $user->wallet()->create([
                'balance_yen' => 0,
                'locked_balance_yen' => 0,
            ]);
        }

        /** @var WalletTransaction $transaction */
        $transaction = $wallet->transactions()->create([
            'type' => 'deposit',
            'status' => 'pending',
            'amount_yen' => $amountYen,
            'provider' => $provider,
            'provider_reference' => $providerReference,
            'requested_by_user_id' => $user->id,
            'memo' => $memo,
            'receipt_path' => $receiptPath,
        ]);

        // Notify admins
        $admins = User::where('role', UserRole::Admin->value)->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminNewDepositRequestNotification($transaction, $user));
        }

        return $transaction;
    }

    public function approveDeposit(WalletTransaction $transaction, ?User $admin = null, ?string $memo = null): WalletTransaction
    {
        if ($transaction->type !== 'deposit') {
            throw ValidationException::withMessages([
                'transaction' => 'Only deposit transactions can be approved.',
            ]);
        }

        if ($transaction->status !== 'pending') {
            throw ValidationException::withMessages([
                'transaction' => 'Only pending transactions can be approved.',
            ]);
        }

        return DB::transaction(function () use ($transaction, $admin, $memo): WalletTransaction {
            $transaction->refresh();

            if ($transaction->status !== 'pending') {
                throw ValidationException::withMessages([
                    'transaction' => 'This transaction has already been processed.',
                ]);
            }

            $wallet = $transaction->wallet()->lockForUpdate()->firstOrFail();

            $wallet->forceFill([
                'balance_yen' => $wallet->balance_yen + max(0, (int) $transaction->amount_yen),
            ])->save();

            $transaction->forceFill([
                'status' => 'approved',
                'approved_by_user_id' => $admin?->id,
                'approved_at' => now(),
                'memo' => $memo ?? $transaction->memo,
            ])->save();

            $wallet->user->notify(new DepositApprovedNotification((int) $transaction->amount_yen, $transaction->memo));

            return $transaction;
        });
    }

    public function rejectDeposit(WalletTransaction $transaction, ?User $admin = null, ?string $memo = null): WalletTransaction
    {
        if ($transaction->type !== 'deposit') {
            throw ValidationException::withMessages([
                'transaction' => 'Only deposit transactions can be rejected.',
            ]);
        }

        if ($transaction->status !== 'pending') {
            throw ValidationException::withMessages([
                'transaction' => 'Only pending transactions can be rejected.',
            ]);
        }

        $transaction->forceFill([
            'status' => 'rejected',
            'approved_by_user_id' => $admin?->id,
            'approved_at' => now(),
            'memo' => $memo ?? $transaction->memo,
        ])->save();

        return $transaction;
    }

    public function requestWithdrawal(User $user, int $amountYen, ?string $destinationType = null, ?array $destinationMeta = null, ?string $memo = null, ?string $transactionId = null, ?string $receiptPath = null): WithdrawalRequest
    {
        $wallet = $user->wallet;

        if ($wallet === null) {
            throw ValidationException::withMessages([
                'wallet' => 'Wallet not found.',
            ]);
        }

        if ($amountYen <= 0) {
            throw ValidationException::withMessages([
                'amount_yen' => 'Withdrawal amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use ($user, $amountYen, $destinationType, $destinationMeta, $memo, $transactionId, $receiptPath): WithdrawalRequest {
            $wallet = $user->wallet()->lockForUpdate()->firstOrFail();

            $availableYen = max(0, (int) $wallet->balance_yen - (int) $wallet->locked_balance_yen - (int) $wallet->withdrawal_locked_yen);

            if ($availableYen < $amountYen) {
                throw ValidationException::withMessages([
                    'amount_yen' => 'Insufficient wallet balance.',
                ]);
            }

            $wallet->forceFill([
                'withdrawal_locked_yen' => (int) $wallet->withdrawal_locked_yen + $amountYen,
            ])->save();

            /** @var WithdrawalRequest $withdrawal */
            $withdrawal = WithdrawalRequest::query()->create([
                'user_id' => $user->id,
                'amount_yen' => $amountYen,
                'status' => 'pending',
                'destination_type' => $destinationType,
                'destination_meta' => $destinationMeta,
                'memo' => $memo,
                'transaction_id' => $transactionId,
                'receipt_path' => $receiptPath,
            ]);

            $wallet->transactions()->create([
                'type' => 'withdrawal',
                'status' => 'pending',
                'amount_yen' => -$amountYen,
                'provider' => null,
                'requested_by_user_id' => $user->id,
                'memo' => $memo,
                'receipt_path' => $receiptPath,
                'meta' => [
                    'withdrawal_request_id' => $withdrawal->id,
                    'destination_type' => $destinationType,
                    'destination_meta' => $destinationMeta,
                ],
            ]);

            return $withdrawal;
        });
    }

    public function approveWithdrawal(WithdrawalRequest $withdrawal, ?User $admin = null, ?string $memo = null): WithdrawalRequest
    {
        if ($withdrawal->status !== 'pending') {
            throw ValidationException::withMessages([
                'withdrawal' => 'Only pending withdrawals can be approved.',
            ]);
        }

        return DB::transaction(function () use ($withdrawal, $admin, $memo): WithdrawalRequest {
            $withdrawal->refresh();

            if ($withdrawal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'withdrawal' => 'This withdrawal has already been processed.',
                ]);
            }

            $wallet = $withdrawal->user->wallet()->lockForUpdate()->firstOrFail();

            $amountYen = (int) $withdrawal->amount_yen;
            if ((int) $wallet->withdrawal_locked_yen < $amountYen) {
                throw ValidationException::withMessages([
                    'withdrawal' => 'Withdrawal lock mismatch. Please reconcile wallet locks before approval.',
                ]);
            }

            if ((int) $wallet->balance_yen < $amountYen) {
                throw ValidationException::withMessages([
                    'withdrawal' => 'Insufficient wallet balance to finalize withdrawal.',
                ]);
            }

            $wallet->forceFill([
                'balance_yen' => (int) $wallet->balance_yen - $amountYen,
                'withdrawal_locked_yen' => (int) $wallet->withdrawal_locked_yen - $amountYen,
            ])->save();

            $transaction = $wallet->transactions()
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->where('meta->withdrawal_request_id', $withdrawal->id)
                ->latest()
                ->first();

            if ($transaction !== null) {
                $transaction->forceFill([
                    'status' => 'approved',
                    'approved_by_user_id' => $admin?->id,
                    'approved_at' => now(),
                    'memo' => $memo ?? $transaction->memo,
                ])->save();
            }

            $withdrawal->forceFill([
                'status' => 'approved',
                'approved_by_user_id' => $admin?->id,
                'approved_at' => now(),
                'memo' => $memo ?? $withdrawal->memo,
            ])->save();

            $withdrawal->user->notify(new WithdrawalProcessedNotification((int) $withdrawal->amount_yen, 'approved', $withdrawal->memo));

            return $withdrawal;
        });
    }

    public function rejectWithdrawal(WithdrawalRequest $withdrawal, ?User $admin = null, ?string $memo = null): WithdrawalRequest
    {
        if ($withdrawal->status !== 'pending') {
            throw ValidationException::withMessages([
                'withdrawal' => 'Only pending withdrawals can be rejected.',
            ]);
        }

        return DB::transaction(function () use ($withdrawal, $admin, $memo): WithdrawalRequest {
            $withdrawal->refresh();

            if ($withdrawal->status !== 'pending') {
                throw ValidationException::withMessages([
                    'withdrawal' => 'This withdrawal has already been processed.',
                ]);
            }

            $wallet = $withdrawal->user->wallet()->lockForUpdate()->firstOrFail();

            $amountYen = (int) $withdrawal->amount_yen;
            $wallet->forceFill([
                'withdrawal_locked_yen' => max(0, (int) $wallet->withdrawal_locked_yen - $amountYen),
            ])->save();

            $transaction = $wallet->transactions()
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->where('meta->withdrawal_request_id', $withdrawal->id)
                ->latest()
                ->first();

            if ($transaction !== null) {
                $transaction->forceFill([
                    'status' => 'rejected',
                    'approved_by_user_id' => $admin?->id,
                    'approved_at' => now(),
                    'memo' => $memo ?? $transaction->memo,
                ])->save();
            }

            $withdrawal->forceFill([
                'status' => 'rejected',
                'approved_by_user_id' => $admin?->id,
                'approved_at' => now(),
                'memo' => $memo ?? $withdrawal->memo,
            ])->save();

            $withdrawal->user->notify(new WithdrawalProcessedNotification((int) $withdrawal->amount_yen, 'rejected', $withdrawal->memo));

            return $withdrawal;
        });
    }
}
