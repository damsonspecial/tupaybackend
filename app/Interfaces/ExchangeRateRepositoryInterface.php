<?php

namespace App\Interfaces;

use App\Models\ExchangeRate;

interface ExchangeRateRepositoryInterface
{
    public function getRate(string $from, string $to): ?float;

    public function updateRate(string $from, string $to, float $rate): ExchangeRate;
}
