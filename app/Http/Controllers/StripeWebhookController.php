<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, WalletService $walletService): Response
    {
        $webhookSecret = (string) env('STRIPE_WEBHOOK_SECRET', '');
        if ($webhookSecret === '') {
            return response('Webhook secret not configured.', 500);
        }

        $signature = (string) $request->header('Stripe-Signature', '');
        if ($signature === '') {
            return response('Missing signature.', 400);
        }

        try {
            $event = Webhook::constructEvent(
                payload: (string) $request->getContent(),
                sigHeader: $signature,
                secret: $webhookSecret,
            );
        } catch (\UnexpectedValueException) {
            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response('Ignored.', 200);
        }

        /** @var array<string, mixed> $data */
        $data = (array) ($event->data?->object ?? []);
        /** @var array<string, mixed> $metadata */
        $metadata = (array) ($data['metadata'] ?? []);
        $transactionId = $metadata['wallet_transaction_id'] ?? null;

        if (! is_numeric($transactionId)) {
            return response('Missing transaction metadata.', 200);
        }

        /** @var WalletTransaction|null $transaction */
        $transaction = WalletTransaction::query()
            ->whereKey((int) $transactionId)
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->first();

        if ($transaction === null) {
            return response('Already processed.', 200);
        }

        $walletService->approveDeposit($transaction, null, 'Stripe webhook: checkout.session.completed');

        return response('OK', 200);
    }
}
