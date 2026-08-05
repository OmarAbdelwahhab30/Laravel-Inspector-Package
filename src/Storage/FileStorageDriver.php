<?php

namespace OmarAbdulwahhab\LaravelInspector\Storage;

use OmarAbdulwahhab\LaravelInspector\Contracts\StorageDriver;

class FileStorageDriver implements StorageDriver
{
    public function __construct(
        private readonly string $path,
    ) {
    }

    public function put(string $id, array $data): void
    {
        $this->ensureDirectoryExists();

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $destination = $this->pathFor($id);

        // Write to a temp file in the same directory then rename, so a
        // concurrent read (e.g. the extension polling right after the
        // response header arrives) never sees a partially-written file —
        // rename() is atomic on both POSIX and NTFS within the same volume.
        $temp = $destination . '.' . bin2hex(random_bytes(4)) . '.tmp';
        file_put_contents($temp, $json);
        rename($temp, $destination);
    }

    public function get(string $id): ?array
    {
        $file = $this->pathFor($id);

        if (! is_file($file)) {
            return null;
        }

        $contents = file_get_contents($file);

        if ($contents === false) {
            return null;
        }

        return json_decode($contents, true);
    }

    public function prune(int $olderThanMinutes): int
    {
        if (! is_dir($this->path)) {
            return 0;
        }

        $cutoff = time() - ($olderThanMinutes * 60);
        $removed = 0;

        foreach (glob(rtrim($this->path, '/\\') . '/*.json') ?: [] as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $removed++;
            }
        }

        return $removed;
    }

    public function latest(int $limit = 50): array
    {
        if (! is_dir($this->path)) {
            return [];
        }

        $files = glob(rtrim($this->path, '/\\') . '/*.json') ?: [];
        
        if (empty($files)) {
            return [];
        }

        usort($files, function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        $files = array_slice($files, 0, $limit);
        
        $results = [];
        foreach ($files as $file) {
            $contents = file_get_contents($file);
            if ($contents !== false) {
                $decoded = json_decode($contents, true);
                if (is_array($decoded)) {
                    $results[] = [
                        'id' => basename($file, '.json'),
                        'request' => $decoded['request'] ?? [],
                        'response' => $decoded['response'] ?? [],
                    ];
                }
            }
        }
        
        return $results;
    }

    private function pathFor(string $id): string
    {
        return rtrim($this->path, '/\\') . '/' . basename($id) . '.json';
    }

    private function ensureDirectoryExists(): void
    {
        if (! is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $gitignorePath = rtrim($this->path, '/\\') . '/.gitignore';
        if (! is_file($gitignorePath)) {
            file_put_contents($gitignorePath, "*\n!.gitignore\n");
        }
    }
}
