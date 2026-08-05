<?php

namespace OmarAbdulwahhab\LaravelInspector\DTO;

use JsonSerializable;

final class ControllerData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $class,
        public readonly ?string $method,
        public readonly ?string $file,
        public readonly ?int $line,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'class' => $this->class,
            'method' => $this->method,
            'file' => $this->file,
            'line' => $this->line,
        ];
    }
}
