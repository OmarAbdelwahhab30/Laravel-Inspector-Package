<?php

namespace OmarAbdulwahhab\LaravelInspector\Http;

use Illuminate\Http\JsonResponse;
use OmarAbdulwahhab\LaravelInspector\Contracts\StorageDriver;
use OmarAbdulwahhab\LaravelInspector\Support\Enabled;

class DevtoolsController
{
    public function show(string $id, StorageDriver $storage): JsonResponse
    {
        // Re-checked at runtime as defense in depth even though this route
        // is only registered at all when Enabled::check() held at boot time.
        if (! Enabled::check()) {
            abort(404);
        }

        // Request IDs are always server-generated UUID v4s (see
        // AssignRequestId); rejecting anything else here is defense in depth
        // on top of FileStorageDriver's own basename()-based path handling.
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            abort(404);
        }

        $snapshot = $storage->get($id);

        if ($snapshot === null) {
            abort(404);
        }

        return response()->json($snapshot);
    }

    public function dashboard()
    {
        if (! Enabled::check()) {
            abort(404);
        }

        return view('laravel-inspector::dashboard');
    }

    public function latest(StorageDriver $storage): JsonResponse
    {
        if (! Enabled::check()) {
            abort(404);
        }

        return response()->json($storage->latest());
    }
}
