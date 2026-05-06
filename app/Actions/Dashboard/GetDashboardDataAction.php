<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use App\Models\ExchangeRate;
use App\Enums\TransactionType;

class GetDashboardDataAction
{
    public function execute(User $user): array
    {
        $wallets = $user->wallets;
        
        $recentTransactions = $user->wallets()
            ->with(['transactions' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get()
            ->pluck('transactions')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $rates = ExchangeRate::where('is_active', true)->get();

        return [
            'wallets' => $wallets,
            'recent_transactions' => $recentTransactions,
            'exchange_rates' => $rates,
            'summary' => [
                'total_balance_ngn' => $wallets->where('currency', \App\Enums\CurrencyCode::NGN)->first()->balance ?? 0,
                'total_balance_cny' => $wallets->where('currency', \App\Enums\CurrencyCode::CNY)->first()->balance ?? 0,
            ]
        ];
    }
}
