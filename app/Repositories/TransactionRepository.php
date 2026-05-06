<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function getHistoryByWallet(int $walletId, int $perPage = 15): LengthAwarePaginator
    {
        return Transaction::where('wallet_id', $walletId)
            ->latest()
            ->paginate($perPage);
    }
}
