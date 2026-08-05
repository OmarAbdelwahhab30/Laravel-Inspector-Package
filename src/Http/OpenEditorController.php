<?php

namespace OmarAbdulwahhab\LaravelInspector\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OmarAbdulwahhab\LaravelInspector\Support\Enabled;
use OmarAbdulwahhab\LaravelInspector\Support\LaunchEditor;

class OpenEditorController
{
    public function open(Request $request, LaunchEditor $launcher): JsonResponse
    {
        // Re-checked at runtime as defense in depth even though this route
        // is only registered at all when Enabled::check() held at boot time.
        if (! Enabled::check()) {
            abort(404);
        }

        $file = (string) $request->query('file', '');

        // is_file() doubles as the path-traversal guard: it resolves the
        // path and only proceeds if it names a real file on this machine,
        // so there is nothing here for a crafted "file" value to exploit
        // beyond confirming whether an arbitrary path exists.
        if ($file === '' || ! is_file($file)) {
            return response()->json([
                'ok' => false,
                'message' => 'File not found on this machine.',
            ], 404);
        }

        $line = $request->query('line');
        $line = is_numeric($line) ? (int) $line : null;

        $opened = $launcher->open($file, $line);

        return response()->json([
            'ok' => $opened,
            'message' => $opened ? null : 'No running editor could be detected on this machine.',
        ], $opened ? 200 : 422);
    }
}
