<?php

namespace App\Http\Controllers;

use App\Actions\Wallet\SwapCurrencyAction;
use App\Http\Requests\Wallet\SwapCurrencyRequest;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function swap(SwapCurrencyRequest $request, SwapCurrencyAction $action)
    {
        $result = $action->execute(
            $request->user(),
            $request->amount,
            $request->from_currency,
            $request->to_currency
        );

        return $this->success($result, 'Currency swap completed successfully');
    }

    public function ledger(int $walletId, TransactionRepositoryInterface $repository)
    {
        $history = $repository->getHistoryByWallet($walletId);

        return $this->success($history, 'Ledger history retrieved');
    }
}
