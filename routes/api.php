<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/2fa/verify', [AuthController::class, 'verify2fa'])->middleware('throttle:3,1');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/rates', [ExchangeRateController::class, 'index']);
    
    // High-value actions protected by 2FA gate
    Route::middleware('2fa.verified')->group(function () {
        Route::post('/swap', [WalletController::class, 'swap']);
    });

    Route::get('/ledger/{wallet_id}', [WalletController::class, 'ledger']);
});

// Webhook endpoint with signature verification
Route::post('/webhooks/settlement', [WebhookController::class, 'settlement'])
    ->middleware('webhook.signature');
