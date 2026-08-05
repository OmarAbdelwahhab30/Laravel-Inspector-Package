<?php

namespace OmarAbdulwahhab\LaravelInspector\DTO;

use JsonSerializable;

final class ResponseData implements JsonSerializable
{
    public function __construct(
        public readonly int $status,
        public readonly float $duration,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => $this->status,
            'duration' => $this->duration,
        ];
    }
}
