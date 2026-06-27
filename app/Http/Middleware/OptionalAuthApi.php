<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

/**
 * Optional API authentication: tries to authenticate when Bearer token is present,
 * but does not abort when missing or invalid. Used for routes that support both
 * authenticated users and guests (e.g. order/list).
 */
class OptionalAuthApi
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            try {
                auth('api')->authenticate();
            } catch (AuthenticationException $e) {
                // Continue as unauthenticated — do not rethrow
            }
        }

        return $next($request);
    }
}
