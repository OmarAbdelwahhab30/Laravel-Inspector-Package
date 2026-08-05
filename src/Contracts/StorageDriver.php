<?php

namespace OmarAbdulwahhab\LaravelInspector\Contracts;

interface StorageDriver
{
    public function put(string $id, array $data): void;

    public function get(string $id): ?array;

    /**
     * Deletes snapshots older than the given age and returns how many were removed.
     */
    public function prune(int $olderThanMinutes): int;

    /**
     * Returns a list of the most recent snapshots.
     */
    public function latest(int $limit = 50): array;
}
