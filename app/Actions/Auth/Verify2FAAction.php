<?php

namespace App\Actions\Auth;

use App\Models\User;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use Illuminate\Validation\ValidationException;

class Verify2FAAction
{
    public function execute(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            throw new \Exception("2FA is not enabled for this user.");
        }

        $isValid = Google2FA::verifyKey($user->two_factor_secret, $code);

        if (!$isValid) {
            throw ValidationException::withMessages([
                'code' => ['Invalid 2FA code.'],
            ]);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return true;
    }
}
