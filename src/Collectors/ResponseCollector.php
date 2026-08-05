<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Http\Request;
use OmarAbdulwahhab\LaravelInspector\Contracts\Collector;
use OmarAbdulwahhab\LaravelInspector\DTO\ResponseData;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;
use Symfony\Component\HttpFoundation\Response;

class ResponseCollector implements Collector
{
    public function __construct(
        private readonly Recorder $recorder,
    ) {
    }

    public function collect(Request $request, Response $response): ResponseData
    {
        $durationMs = (microtime(true) - $this->recorder->getStartTime()) * 1000;

        return new ResponseData(
            status: $response->getStatusCode(),
            duration: round($durationMs, 2),
        );
    }
}
