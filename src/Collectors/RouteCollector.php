<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Http\Request;
use OmarAbdulwahhab\LaravelInspector\Contracts\Collector;
use OmarAbdulwahhab\LaravelInspector\DTO\RequestData;
use OmarAbdulwahhab\LaravelInspector\DTO\RouteData;
use Symfony\Component\HttpFoundation\Response;

class RouteCollector implements Collector
{
    public function collect(Request $request, Response $response): RequestData
    {
        $route = $request->route();

        $routeData = $route
            ? new RouteData(uri: $route->uri(), name: $route->getName())
            : null;

        return new RequestData(
            method: $request->method(),
            url: $request->path(),
            route: $routeData,
        );
    }
}
