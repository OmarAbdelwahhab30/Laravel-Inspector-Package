<?php

namespace OmarAbdulwahhab\LaravelInspector\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Chrome extension's panel page always calls /__devtools/* cross-origin
 * (from a chrome-extension:// origin), and there's no reason to expect the
 * host app's own CORS config — if it even has one — to cover these paths;
 * Laravel's default CORS middleware only matches api/* by convention. So
 * this package sets its own CORS header rather than depending on
 * app-level configuration it can't assume exists.
 */
class AllowExtensionOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
