<?php

namespace OmarAbdulwahhab\LaravelInspector\Services;

/**
 * Request-scoped singleton: one instance per request, bound in the container
 * as early as Middleware\AssignRequestId resolves it (before the controller
 * runs), so startTime reflects the true beginning of request handling and
 * any future accumulating collectors (queries, events) have somewhere to
 * push entries as they happen.
 */
class Recorder
{
    private string $id;

    private float $startTime;

    /** @var array<string, mixed> */
    private array $entries = [];

    /** @var array<string, mixed>|null */
    private ?array $snapshot = null;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function hasId(): bool
    {
        return isset($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function put(string $key, mixed $value): void
    {
        $this->entries[$key] = $value;
    }

    public function push(string $key, mixed $value): void
    {
        $this->entries[$key][] = $value;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->entries;
    }

    /**
     * Stores the fully-assembled snapshot (built by SnapshotBuilder during the
     * RequestHandled listener) so the terminating() callback — which has no
     * direct access to the request/response objects — can persist it.
     *
     * @param array<string, mixed> $snapshot
     */
    public function setSnapshot(array $snapshot): void
    {
        $this->snapshot = $snapshot;
    }

    /** @return array<string, mixed>|null */
    public function getSnapshot(): ?array
    {
        return $this->snapshot;
    }
}
