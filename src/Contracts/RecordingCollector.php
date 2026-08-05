<?php

namespace OmarAbdulwahhab\LaravelInspector\Contracts;

/**
 * Hook-based accumulator: registers itself against framework events/listeners
 * as they occur throughout the request (e.g. each SQL query, each fired event)
 * and flushes the accumulated entries at the end. Not used by any MVP collector
 * yet (Route/Controller/Response are all computed-at-end via Collector), but
 * defined now so future collectors (Query, Event, Job, Log) have a stable
 * contract to implement without changing the Recorder or SnapshotBuilder.
 */
interface RecordingCollector
{
    public function register(): void;

    public function flush(): array;
}
