<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => Google2FA::generateSecretKey(),
        ]);

        // Create NGN wallet with 1,000,000 NGN (in cents)
        $ngnWallet = $user->wallets()->create([
            'currency' => \App\Enums\CurrencyCode::NGN,
            'balance' => 100000000,
            'status' => \App\Enums\WalletStatus::ACTIVE,
        ]);

        // Create initial ledger entry
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $ngnWallet->id,
            'type' => \App\Enums\TransactionType::CREDIT,
            'amount' => 100000000,
            'balance_before' => 0,
            'balance_after' => 100000000,
            'description' => 'Initial deposit',
            'reference' => 'INITIAL_DEPOSIT_' . \Illuminate\Support\Str::random(8),
        ]);

        // Create CNY wallet with 0 balance
        $user->wallets()->create([
            'currency' => \App\Enums\CurrencyCode::CNY,
            'balance' => 0,
            'status' => \App\Enums\WalletStatus::ACTIVE,
        ]);

        $this->call([
            ExchangeRateSeeder::class,
        ]);
    }
}
