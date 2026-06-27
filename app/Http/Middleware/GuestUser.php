<?php

namespace App\Http\Middleware;

use App\Models\GuestUser as GuestUserModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GuestUser
{
    /**
     * Handle an incoming request.
     * Validates guest-id to prevent IDOR: guest must exist and (optionally) IP must match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guestId = $request->header('guest-id') ?? config('guest_id');

        if ($guestId !== null && $guestId !== '') {
            $guest = GuestUserModel::find($guestId);
            $valid = $guest !== null;
            if ($valid && config('app.guest_validate_ip')) {
                $valid = $guest->ip_address === $request->ip();
            }
            if (!$valid) {
                $guestId = null;
            }
        }

        Config::set('guest_id', $guestId);
        return $next($request);
    }
}
