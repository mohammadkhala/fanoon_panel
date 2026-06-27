<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        return redirect()->route('admin.auth.login');
    }
}
