<?php

namespace App\Repositories;

use App\Models\ExchangeRate;
use App\Interfaces\ExchangeRateRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    public function getRate(string $from, string $to): ?float
    {
        $cacheKey = "exchange_rate_{$from}_{$to}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($from, $to) {
            $rate = ExchangeRate::where('from_currency', $from)
                ->where('to_currency', $to)
                ->where('is_active', true)
                ->first();

            return $rate ? (float) $rate->rate : null;
        });
    }

    public function updateRate(string $from, string $to, float $rate): ExchangeRate
    {
        $exchangeRate = ExchangeRate::updateOrCreate(
            ['from_currency' => $from, 'to_currency' => $to],
            ['rate' => $rate, 'is_active' => true]
        );

        // Invalidate cache
        Cache::forget("exchange_rate_{$from}_{$to}");

        return $exchangeRate;
    }
}
