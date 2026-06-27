<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($request->isMethod('GET')
                && ! $request->ajax()
                && ! $request->is('admin*')
                && ! $request->is('build/*', 'storage/*', 'up')
                && Schema::hasTable('visits')) {
                Visit::create([
                    'url' => substr($request->path(), 0, 500),
                    'ip' => $request->ip(),
                    'referer' => substr((string) $request->headers->get('referer'), 0, 500) ?: null,
                    'user_id' => $request->user()?->id,
                    'visited_on' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            // لا نُفشل الطلب بسبب التتبّع
        }

        return $response;
    }
}
