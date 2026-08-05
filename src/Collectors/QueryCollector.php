<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use OmarAbdulwahhab\LaravelInspector\Contracts\RecordingCollector;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;
use OmarAbdulwahhab\LaravelInspector\Support\Backtrace;

class QueryCollector implements RecordingCollector
{
    public function __construct(
        private readonly Recorder $recorder
    ) {
    }

    public function register(): void
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
            $location = Backtrace::detectFileAndLine();
            $timeMs = $event->time;

            // Approximate the start time of the query
            $offset = (microtime(true) - $this->recorder->getStartTime()) * 1000 - $timeMs;
            if ($offset < 0) {
                $offset = 0;
            }

            $entry = [
                'sql' => $event->sql,
                'bindings' => $this->sanitizeBindings($event->sql, $event->bindings),
                'time' => $timeMs,
                'connection' => $event->connectionName,
                'file' => $location['file'],
                'line' => $location['line'],
                'is_slow' => $timeMs > config('devtools.slow_query_threshold', 50),
            ];

            $this->recorder->push('queries', $entry);

            $this->recorder->push('timeline', [
                'type' => 'query',
                'label' => $event->sql,
                'duration' => $timeMs,
                'offset' => $offset,
            ]);
        });
    }

    public function flush(): array
    {
        return [];
    }

    /**
     * Redact bindings whose column name (immediately preceding their "?"
     * placeholder in the SQL) matches a sensitive pattern — snapshots are
     * persisted as plaintext JSON on disk, so raw passwords/tokens must
     * never round-trip into them. Purely heuristic (won't catch every SQL
     * shape) but covers the common "column = ?" case.
     */
    private function sanitizeBindings(string $sql, array $bindings): array
    {
        $patterns = config('devtools.redact_bindings', []);

        if (empty($patterns)) {
            return $bindings;
        }

        $sensitiveIndexes = [];
        $offset = 0;
        $placeholderIndex = 0;

        while (($pos = strpos($sql, '?', $offset)) !== false) {
            $before = substr($sql, 0, $pos);

            if (preg_match('/([a-zA-Z_][a-zA-Z0-9_.]*)\s*(?:=|<>|!=|like)\s*$/i', $before, $matches) === 1) {
                if (Str::is($patterns, strtolower($matches[1]))) {
                    $sensitiveIndexes[] = $placeholderIndex;
                }
            }

            $placeholderIndex++;
            $offset = $pos + 1;
        }

        if (empty($sensitiveIndexes)) {
            return $bindings;
        }

        return array_values(array_map(
            fn ($binding, $index) => in_array($index, $sensitiveIndexes, true) ? '[REDACTED]' : $binding,
            $bindings,
            array_keys($bindings)
        ));
    }
}
