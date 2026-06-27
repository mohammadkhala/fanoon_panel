<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يصل أحيانًا من الواجهة baseUrl ينتهي بـ / ثم يُضاف /image-proxy → //image-proxy فيرجع 404.
 */
class CollapseDuplicatePathSlashes
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $rawUri = $request->server('REQUEST_URI', '');
        if ($rawUri === '' || ! str_contains($rawUri, '//')) {
            return $next($request);
        }

        $questionPos = strpos($rawUri, '?');
        $pathPart = $questionPos === false ? $rawUri : substr($rawUri, 0, $questionPos);
        $queryPart = $questionPos === false ? '' : substr($rawUri, $questionPos);

        if ($pathPart === '' || ! str_contains($pathPart, '//')) {
            return $next($request);
        }

        $collapsed = preg_replace('#/+#', '/', $pathPart);
        if (! is_string($collapsed) || $collapsed === $pathPart) {
            return $next($request);
        }

        return redirect()->to($collapsed . $queryPart, 301);
    }
}
