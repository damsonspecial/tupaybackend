<?php

namespace App\Interfaces;

use App\Enums\TransactionType;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findByUserAndCurrency(int $userId, string $currency): ?Wallet;

    public function updateBalance(Wallet $wallet, int $amount, TransactionType $type): Wallet;
}
