<?php

namespace App\Actions\Wallet;

use App\Enums\TransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Interfaces\ExchangeRateRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SwapCurrencyAction
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {}

    public function execute(User $user, int $amountInCents, string $fromCurrency = 'NGN', string $toCurrency = 'CNY'): array
    {
        // 1. Acquire Redis Atomic Lock to prevent concurrent swaps for this user
        $lock = Cache::lock("swap_lock_{$user->id}", 10);

        return $lock->block(5, function () use ($user, $amountInCents, $fromCurrency, $toCurrency) {
            return DB::transaction(function () use ($user, $amountInCents, $fromCurrency, $toCurrency) {
                
                // 2. Find wallets
                $fromWallet = $this->walletRepository->findByUserAndCurrency($user->id, $fromCurrency);
                $toWallet = $this->walletRepository->findByUserAndCurrency($user->id, $toCurrency);

                if (!$fromWallet || !$toWallet) {
                    throw new InvalidArgumentException("Required wallets not found.");
                }

                // 3. Get Exchange Rate
                $rate = $this->exchangeRateRepository->getRate($fromCurrency, $toCurrency);
                if (!$rate) {
                    throw new InvalidArgumentException("Exchange rate not available.");
                }

                // 4. Calculate converted amount using BCMath for high precision
                $convertedAmount = (int) bcmul((string) $amountInCents, (string) $rate, 0);

                // 5. Debit source wallet
                $fromBalanceBefore = $fromWallet->balance;
                $this->walletRepository->updateBalance($fromWallet, $amountInCents, TransactionType::DEBIT);
                $fromBalanceAfter = $fromWallet->fresh()->balance;

                // 6. Credit destination wallet
                $toBalanceBefore = $toWallet->balance;
                $this->walletRepository->updateBalance($toWallet, $convertedAmount, TransactionType::CREDIT);
                $toBalanceAfter = $toWallet->fresh()->balance;

                // 7. Create Ledger Entries
                $reference = Str::uuid()->toString();

                $this->transactionRepository->create([
                    'user_id' => $user->id,
                    'wallet_id' => $fromWallet->id,
                    'type' => TransactionType::DEBIT,
                    'amount' => $amountInCents,
                    'balance_before' => $fromBalanceBefore,
                    'balance_after' => $fromBalanceAfter,
                    'description' => "Swap {$fromCurrency} to {$toCurrency}",
                    'reference' => "SWAP-OUT-{$reference}",
                    'metadata' => [
                        'rate' => $rate,
                        'converted_amount' => $convertedAmount,
                        'to_currency' => $toCurrency,
                    ],
                ]);

                $this->transactionRepository->create([
                    'user_id' => $user->id,
                    'wallet_id' => $toWallet->id,
                    'type' => TransactionType::CREDIT,
                    'amount' => $convertedAmount,
                    'balance_before' => $toBalanceBefore,
                    'balance_after' => $toBalanceAfter,
                    'description' => "Swap from {$fromCurrency}",
                    'reference' => "SWAP-IN-{$reference}",
                    'metadata' => [
                        'rate' => $rate,
                        'original_amount' => $amountInCents,
                        'from_currency' => $fromCurrency,
                    ],
                ]);

                return [
                    'from_amount' => $amountInCents,
                    'to_amount' => $convertedAmount,
                    'rate' => $rate,
                    'reference' => $reference,
                ];
            });
        });
    }
}
