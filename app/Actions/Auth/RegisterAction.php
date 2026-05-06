<?php

namespace App\Actions\Auth;

use App\Enums\CurrencyCode;
use App\Enums\WalletStatus;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class RegisterAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'two_factor_secret' => Google2FA::generateSecretKey(),
            ]);

            $user->wallets()->create([
                'currency' => CurrencyCode::NGN,
                'balance' => 0,
                'status' => WalletStatus::ACTIVE,
            ]);

            // Create CNY wallet
            $user->wallets()->create([
                'currency' => CurrencyCode::CNY,
                'balance' => 0,
                'status' => WalletStatus::ACTIVE,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
                'two_factor_qr_code' => Google2FA::getQRCodeInline(
                    config('app.name'),
                    $user->email,
                    $user->two_factor_secret
                ),
                'two_factor_secret' => $user->two_factor_secret,
                'two_factor_setup_url' => "otpauth://totp/".config('app.name').":".$user->email."?secret=".$user->two_factor_secret."&issuer=".config('app.name'),
            ];
        });
    }
}
