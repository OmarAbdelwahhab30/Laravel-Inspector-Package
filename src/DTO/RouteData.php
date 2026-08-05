<?php

namespace OmarAbdulwahhab\LaravelInspector\DTO;

use JsonSerializable;

final class RouteData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $uri,
        public readonly ?string $name,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'uri' => $this->uri,
            'name' => $this->name,
        ];
    }
}
