<?php

namespace OmarAbdulwahhab\LaravelInspector\Services;

use Illuminate\Http\Request;
use OmarAbdulwahhab\LaravelInspector\Collectors\ControllerCollector;
use OmarAbdulwahhab\LaravelInspector\Collectors\ResponseCollector;
use OmarAbdulwahhab\LaravelInspector\Collectors\RouteCollector;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs each enabled computed-Collector (per config('devtools.collectors')) and
 * merges the result with whatever the Recorder has accumulated so far, storing
 * the assembled snapshot back onto the Recorder for the terminating() callback
 * to persist. Adding a new collector means adding one branch here (or, for
 * RecordingCollectors, nothing here at all) and one config entry — no changes
 * to existing collectors.
 */
class SnapshotBuilder
{
    public function __construct(
        private readonly Recorder $recorder,
    ) {
    }

    public function build(Request $request, Response $response): array
    {
        $collectors = config('devtools.collectors', []);

        if ($collectors['route'] ?? false) {
            $this->recorder->put('request', app(RouteCollector::class)->collect($request, $response));
        }

        if ($collectors['controller'] ?? false) {
            $this->recorder->put('controller', app(ControllerCollector::class)->collect($request, $response));
        }

        if ($collectors['response'] ?? false) {
            $this->recorder->put('response', app(ResponseCollector::class)->collect($request, $response));
        }

        return array_merge([
            'id' => $this->recorder->getId(),
            'request' => null,
            'controller' => null,
            'response' => null,
            'queries' => [],
            'events' => [],
            'jobs' => [],
            'timeline' => [],
        ], $this->recorder->all());
    }
}
