<?php

namespace OmarAbdulwahhab\LaravelInspector\DTO;

use JsonSerializable;

final class RequestData implements JsonSerializable
{
    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly ?RouteData $route,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'method' => $this->method,
            'url' => $this->url,
            'route' => $this->route,
        ];
    }
}
