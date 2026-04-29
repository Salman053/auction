<?php

use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Notifications\DepositApprovedNotification;
use App\Notifications\WithdrawalProcessedNotification;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('user can request a deposit and admin can approve it', function () {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create();
    $walletService = app(WalletService::class);

    $transaction = $walletService->requestDeposit($user, 50000, 'stripe', 'Monthly top-up');

    expect($transaction->status)->toBe('pending');
    expect($transaction->amount_yen)->toBe(50000);

    $admin = User::factory()->admin()->create();
    $walletService->approveDeposit($transaction, $admin, 'Approved by admin');

    $user->refresh();
    expect($user->wallet->balance_yen)->toBe(50000);
    expect($transaction->refresh()->status)->toBe('approved');

    Notification::assertSentTo($user, DepositApprovedNotification::class, function ($notification) {
        return $notification->amountYen === 50000;
    });
});

test('user can request a withdrawal and admin can process it', function () {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 100000]);

    $walletService = app(WalletService::class);

    // Request withdrawal
    $withdrawal = $walletService->requestWithdrawal($user, 30000, 'bank_transfer', ['account' => '12345']);

    $user->wallet->refresh();
    expect($user->wallet->withdrawal_locked_yen)->toBe(30000);
    // Balance shouldn't decrease yet, but "available" would be lower
    expect($user->wallet->balance_yen)->toBe(100000);

    // Approve withdrawal
    $admin = User::factory()->admin()->create();
    $walletService->approveWithdrawal($withdrawal, $admin);

    $user->wallet->refresh();
    expect($user->wallet->balance_yen)->toBe(70000);
    expect($user->wallet->withdrawal_locked_yen)->toBe(0);
    expect($withdrawal->refresh()->status)->toBe('approved');

    Notification::assertSentTo($user, WithdrawalProcessedNotification::class, function ($notification) {
        return $notification->status === 'approved' && $notification->amountYen === 30000;
    });
});

test('admin can reject a withdrawal request', function () {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 100000]);

    $walletService = app(WalletService::class);
    $withdrawal = $walletService->requestWithdrawal($user, 30000);

    $admin = User::factory()->admin()->create();
    $walletService->rejectWithdrawal($withdrawal, $admin, 'Insufficient documentation');

    $user->wallet->refresh();
    expect($user->wallet->balance_yen)->toBe(100000); // Balance remains same
    expect($user->wallet->withdrawal_locked_yen)->toBe(0); // Lock is released
    expect($withdrawal->refresh()->status)->toBe('rejected');

    Notification::assertSentTo($user, WithdrawalProcessedNotification::class, function ($notification) {
        return $notification->status === 'rejected';
    });
});

test('user can request a deposit with receipt and transaction ID', function () {
    Storage::fake('public');

    /** @var User $user */
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('receipt.jpg', 100);

    $response = $this->actingAs($user, 'user')->post(route('user.wallet.deposits.store'), [
        'amount_yen' => 5000,
        'provider' => 'bank',
        'memo' => 'Test deposit',
        'transaction_id' => 'TXN-999',
        'receipt' => $file,
    ]);

    $response->assertRedirect();

    $transaction = WalletTransaction::latest()->first();
    expect($transaction->amount_yen)->toBe(5000);
    expect($transaction->provider_reference)->toBe('TXN-999');
    expect($transaction->receipt_path)->not->toBeNull();

    expect(Storage::disk('public')->exists($transaction->receipt_path))->toBeTrue();
});

test('user can request a withdrawal with receipt and transaction ID', function () {
    Storage::fake('public');

    /** @var User $user */
    $user = User::factory()->create();
    $user->wallet->update(['balance_yen' => 10000]);
    $file = UploadedFile::fake()->create('proof.pdf', 100);

    $response = $this->actingAs($user, 'user')->post(route('user.withdrawals.store'), [
        'amount_yen' => 2000,
        'transaction_id' => 'WD-123',
        'receipt' => $file,
    ]);

    $response->assertRedirect();

    $withdrawal = WithdrawalRequest::latest()->first();
    expect($withdrawal->amount_yen)->toBe(2000);
    expect($withdrawal->transaction_id)->toBe('WD-123');
    expect($withdrawal->receipt_path)->not->toBeNull();

    expect(Storage::disk('public')->exists($withdrawal->receipt_path))->toBeTrue();
});
