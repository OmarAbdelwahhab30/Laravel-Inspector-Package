<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Support\Facades\Event;
use OmarAbdulwahhab\LaravelInspector\Contracts\RecordingCollector;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;
use OmarAbdulwahhab\LaravelInspector\Support\Backtrace;

class EventCollector implements RecordingCollector
{
    public function __construct(
        private readonly Recorder $recorder
    ) {
    }

    public function register(): void
    {
        Event::listen('*', function (string $eventName, array $payload) {
            if ($this->shouldIgnore($eventName)) {
                return;
            }

            $location = Backtrace::detectFileAndLine();
            $offset = (microtime(true) - $this->recorder->getStartTime()) * 1000;

            $this->recorder->push('events', [
                'name' => $eventName,
                'payload' => $this->formatPayload($payload),
                'file' => $location['file'],
                'line' => $location['line'],
            ]);

            $this->recorder->push('timeline', [
                'type' => 'event',
                'label' => $eventName,
                'duration' => null,
                'offset' => $offset,
            ]);
        });
    }

    public function flush(): array
    {
        return [];
    }

    private function shouldIgnore(string $eventName): bool
    {
        $ignoredPrefixes = [
            'Illuminate\\',
            'eloquent.',
            'bootstrapped:',
            'bootstrapping:',
            'creating:',
            'created:',
            'updating:',
            'updated:',
            'saving:',
            'saved:',
            'deleting:',
            'deleted:',
            'restoring:',
            'restored:',
            'retrieved:',
            'Laravel\\Octane\\',
        ];

        foreach ($ignoredPrefixes as $prefix) {
            if (str_starts_with($eventName, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function formatPayload(array $payload): array
    {
        return array_map(function ($item) {
            if (is_object($item)) {
                return get_class($item);
            }
            if (is_array($item)) {
                return 'array(' . count($item) . ')';
            }
            return $item;
        }, $payload);
    }
}
