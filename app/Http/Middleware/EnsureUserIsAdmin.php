<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'هذه الصفحة مخصّصة للإدارة فقط.');
        }

        // Super-admins pass through without any route-level check.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // For restricted roles, verify the current route is allowed.
        $routeName = $request->route()?->getName() ?? '';

        if (! $user->canAccessRoute($routeName)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
