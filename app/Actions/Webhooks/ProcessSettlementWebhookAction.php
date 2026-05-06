<?php

namespace App\Actions\Webhooks;

use App\Enums\TransactionType;
use App\Events\SettlementProcessed;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Interfaces\TransactionRepositoryInterface;
use App\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSettlementWebhookAction
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(array $payload): void
    {
        $providerReference = $payload['provider_reference'];
        $amount = $payload['amount']; // In cents
        $walletId = $payload['wallet_id'];
        $userId = $payload['user_id'];

        // 1. Idempotency Check: Check if reference already exists
        $existing = Transaction::where('reference', "WEBHOOK-{$providerReference}")->exists();
        if ($existing) {
            Log::info("Duplicate webhook received: {$providerReference}");
            return;
        }

        DB::transaction(function () use ($providerReference, $amount, $walletId, $userId) {
            $wallet = Wallet::findOrFail($walletId);

            // 2. Update Balance
            $balanceBefore = $wallet->balance;
            $this->walletRepository->updateBalance($wallet, $amount, TransactionType::CREDIT);
            $balanceAfter = $wallet->fresh()->balance;

            // 3. Create Ledger Entry
            $transaction = $this->transactionRepository->create([
                'user_id' => $userId,
                'wallet_id' => $walletId,
                'type' => TransactionType::CREDIT,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Settlement Confirmation: {$providerReference}",
                'reference' => "WEBHOOK-{$providerReference}",
                'metadata' => [
                    'provider_payload' => $payload,
                ],
            ]);

            // 4. Dispatch notification
            event(new SettlementProcessed($transaction));
        });
    }
}
