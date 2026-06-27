<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiPerformanceDebugMiddleware
{
    /**
     * Pass-through middleware for API requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
