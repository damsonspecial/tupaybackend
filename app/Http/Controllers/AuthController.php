<?php

namespace App\Http\Controllers;
 
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\Verify2FAAction;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\Verify2FARequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $action)
    {
        $result = $action->execute($request->validated());

        return $this->success($result, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request, LoginAction $action)
    {
        $result = $action->execute($request->email, $request->password);

        return $this->success($result, 'Login successful');
    }

    public function verify2fa(Verify2FARequest $request, Verify2FAAction $action)
    {
        $action->execute($request->user(), $request->code);

        $tokenId = $request->user()->currentAccessToken()->id;
        Cache::put("2fa_verified_token_{$tokenId}", true, now()->addHour());

        return $this->success(null, '2FA verified successfully');
    }
}
