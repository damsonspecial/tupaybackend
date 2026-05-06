<?php

namespace App\Interfaces;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function getHistoryByWallet(int $walletId, int $perPage = 15): LengthAwarePaginator;
}
