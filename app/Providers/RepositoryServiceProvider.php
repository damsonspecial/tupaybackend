<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Interfaces\WalletRepositoryInterface::class,
            \App\Repositories\WalletRepository::class
        );

        $this->app->bind(
            \App\Interfaces\TransactionRepositoryInterface::class,
            \App\Repositories\TransactionRepository::class
        );

        $this->app->bind(
            \App\Interfaces\ExchangeRateRepositoryInterface::class,
            \App\Repositories\ExchangeRateRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
