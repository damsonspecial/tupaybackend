<?php

namespace App\Repositories;

use App\Enums\TransactionType;
use App\Models\Wallet;
use App\Interfaces\WalletRepositoryInterface;
use InvalidArgumentException;

class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserAndCurrency(int $userId, string $currency): ?Wallet
    {
        return Wallet::where('user_id', $userId)
            ->where('currency', $currency)
            ->first();
    }

    public function updateBalance(Wallet $wallet, int $amount, TransactionType $type): Wallet
    {
        if ($type === TransactionType::DEBIT && $wallet->balance < $amount) {
            throw new InvalidArgumentException('Insufficient funds.');
        }

        if ($type === TransactionType::DEBIT) {
            $wallet->decrement('balance', $amount);
        } else {
            $wallet->increment('balance', $amount);
        }

        return $wallet->fresh();
    }
}
