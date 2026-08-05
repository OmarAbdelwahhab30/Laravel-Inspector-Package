<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Http\Request;
use OmarAbdulwahhab\LaravelInspector\Contracts\Collector;
use OmarAbdulwahhab\LaravelInspector\DTO\ControllerData;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

class ControllerCollector implements Collector
{
    public function collect(Request $request, Response $response): ControllerData
    {
        $action = $request->route()?->getActionName();

        if (! $action || ! str_contains($action, '@')) {
            return new ControllerData(class: null, method: null, file: null, line: null);
        }

        [$class, $method] = explode('@', $action, 2);

        try {
            $reflection = new ReflectionMethod($class, $method);
        } catch (ReflectionException) {
            return new ControllerData(class: $class, method: $method, file: null, line: null);
        }

        return new ControllerData(
            class: $class,
            method: $method,
            file: $reflection->getFileName() ?: null,
            line: $reflection->getStartLine() ?: null,
        );
    }
}
