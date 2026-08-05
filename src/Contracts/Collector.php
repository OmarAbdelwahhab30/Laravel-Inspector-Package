<?php

namespace OmarAbdulwahhab\LaravelInspector\Contracts;

use Illuminate\Http\Request;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Computed-at-end collector: runs once, after the request has finished
 * processing, with both the final Request and Response available.
 */
interface Collector
{
    public function collect(Request $request, Response $response): JsonSerializable;
}
