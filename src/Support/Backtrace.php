<?php

namespace OmarAbdulwahhab\LaravelInspector\Support;

class Backtrace
{
    public static function detectFileAndLine(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($trace as $frame) {
            $file = $frame['file'] ?? null;
            if ($file && ! str_contains($file, 'vendor/laravel/framework') && ! str_contains($file, 'OmarAbdulwahhab/LaravelInspector')) {
                return [
                    'file' => $file,
                    'line' => $frame['line'] ?? null,
                ];
            }
        }

        return ['file' => null, 'line' => null];
    }
}
