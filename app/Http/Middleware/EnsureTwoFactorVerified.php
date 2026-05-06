<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tokenId = $user->currentAccessToken()->id;

        // Check if 2FA is verified in cache for this specific token
        $isVerified = Cache::get("2fa_verified_token_{$tokenId}");

        if (!$isVerified) {
            return response()->json([
                'message' => '2FA verification required for this action.',
            ], 403);
        }

        return $next($request);
    }
}
