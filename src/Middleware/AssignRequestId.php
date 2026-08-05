<?php

namespace OmarAbdulwahhab\LaravelInspector\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;

/**
 * Prepended to the global middleware stack so it runs before the controller
 * (and before any other middleware) — this must happen early so accumulating
 * collectors added later (queries, events) have an ID to tag entries with
 * from the very start of the request.
 *
 * Deliberately does nothing after $next($request): Laravel's HTTP kernel
 * catches exceptions outside the middleware pipeline and renders them to a
 * response before dispatching RequestHandled, so code placed here after
 * $next() would never run for a request that threw. Header attachment and
 * computed-collector work live in the RequestHandled listener instead, which
 * fires on both the success and exception-render paths.
 */
class AssignRequestId
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('OPTIONS') || $request->is(config('devtools.ignore_paths', []))) {
            return $next($request);
        }

        $id = (string) Str::uuid();

        $recorder = app(Recorder::class);
        $recorder->setId($id);

        return $next($request);
    }
}
