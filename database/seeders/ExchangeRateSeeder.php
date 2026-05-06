<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRate::updateOrCreate(
            ['from_currency' => 'NGN', 'to_currency' => 'CNY'],
            ['rate' => 0.0085, 'source' => 'manual', 'is_active' => true]
        );

        ExchangeRate::updateOrCreate(
            ['from_currency' => 'CNY', 'to_currency' => 'NGN'],
            ['rate' => 117.65, 'source' => 'manual', 'is_active' => true]
        );
    }
}
