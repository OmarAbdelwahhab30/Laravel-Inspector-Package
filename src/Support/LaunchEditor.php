<?php

namespace OmarAbdulwahhab\LaravelInspector\Support;

/**
 * Auto-detects a running, supported editor on the machine hosting this
 * Laravel process and opens a file at a given line in it — no user-facing
 * "default editor" setting anywhere. Same UX as Vue DevTools' click-to-open
 * (backed by the `launch-editor` npm package): this only works because in
 * local dev, the machine running `php artisan serve`/Herd is the
 * developer's own machine, so opening a local application here reaches the
 * same desktop the browser is on. Browser JavaScript has no such capability
 * itself, which is why this lives server-side rather than in the extension.
 */
class LaunchEditor
{
    /**
     * Regex (matched against a running process's executable basename) =>
     * canonical binary name used to build the launch command. Ported from
     * the process list the `launch-editor` package matches against.
     */
    private const EDITORS = [
        '/^code[\s-]*insiders(\.exe)?$/i' => 'code-insiders',
        '/^code(\.exe)?$/i' => 'code',
        '/^cursor(\.exe)?$/i' => 'cursor',
        '/^antigravity[\s-]*ide(\.exe)?$/i' => 'antigravity-ide',
        '/^phpstorm(64)?(\.exe)?$/i' => 'phpstorm',
        '/^idea(64)?(\.exe)?$/i' => 'idea',
        '/^webstorm(64)?(\.exe)?$/i' => 'webstorm',
        '/^pycharm(64)?(\.exe)?$/i' => 'pycharm',
        '/^sublime_text(\.exe)?$/i' => 'subl',
        '/^atom(\.exe)?$/i' => 'atom',
        '/^notepad\+\+(\.exe)?$/i' => 'notepad++',
    ];

    public function open(string $file, ?int $line): bool
    {
        $binary = $this->detectRunningEditor() ?? $this->fromEnvOverride();

        if ($binary === null) {
            return false;
        }

        return $this->run($this->buildCommand($binary, $file, $line));
    }

    private function detectRunningEditor(): ?string
    {
        foreach ($this->runningProcessNames() as $name) {
            foreach (self::EDITORS as $pattern => $binary) {
                if (preg_match($pattern, $name) === 1) {
                    return $binary;
                }
            }
        }

        return null;
    }

    /**
     * Escape hatch for editors that never show up as a matchable GUI
     * process (e.g. a terminal-based editor) or aren't in the known list.
     * Set once in .env — not something picked per click, so it doesn't
     * reintroduce a "default editor" setting.
     */
    private function fromEnvOverride(): ?string
    {
        $editor = env('LARAVEL_DEVTOOLS_EDITOR');

        return is_string($editor) && $editor !== '' ? $editor : null;
    }

    /**
     * @return list<string>
     */
    private function runningProcessNames(): array
    {
        $output = match (PHP_OS_FAMILY) {
            'Windows' => $this->shell('powershell -NoProfile -Command "Get-Process | Select-Object -ExpandProperty ProcessName"'),
            'Darwin' => $this->shell('ps -axo comm='),
            default => $this->shell('ps -xo comm='),
        };

        if ($output === null || trim($output) === '') {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', trim($output)) ?: [];
    }

    private function buildCommand(string $bin, string $file, ?int $line): string
    {
        $lineNum = $line ?? 1;

        return match (true) {
            in_array($bin, ['code', 'code-insiders', 'cursor', 'antigravity-ide'], true) =>
                sprintf('%s -g %s', $bin, escapeshellarg("{$file}:{$lineNum}")),
            in_array($bin, ['phpstorm', 'idea', 'webstorm', 'pycharm'], true) =>
                sprintf('%s --line %d %s', $bin, $lineNum, escapeshellarg($file)),
            $bin === 'subl' =>
                sprintf('subl %s', escapeshellarg("{$file}:{$lineNum}")),
            $bin === 'atom' =>
                sprintf('atom %s', escapeshellarg("{$file}:{$lineNum}")),
            $bin === 'notepad++' =>
                sprintf('notepad++ -n%d %s', $lineNum, escapeshellarg($file)),
            default =>
                sprintf('%s %s', escapeshellarg($bin), escapeshellarg($file)),
        };
    }

    /**
     * exec()/shell_exec() read the command's output through a pipe until
     * EOF, and EOF only arrives once every process holding a duplicate of
     * that pipe's write handle has closed it — not just the immediate
     * cmd.exe child. On Windows, the GUI app `start` launches can end up
     * with a handle onto that same pipe (observed: reproducibly, every
     * time an editor is launched with a file argument), so exec() blocks
     * for the editor's entire lifetime instead of returning immediately —
     * confirmed by reproduction, where it only returned once the spawned
     * window was closed, minutes later, freezing the whole PHP dev server
     * (single-worker) for every other request in the meantime.
     *
     * proc_open() with 'file' descriptors (not 'pipe') sidesteps this
     * entirely: stdio is wired straight to the null device, so there is no
     * pipe for anything to inherit, and proc_close() only waits on the
     * immediate `start`/shell wrapper — which exits the moment the editor
     * is launched — not on the editor process itself.
     */
    private function run(string $command): bool
    {
        if (! function_exists('proc_open')) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $this->ensureWindowsUserEnv();
        }

        // /B matters specifically for editors whose CLI is a .cmd wrapper
        // (VS Code, Cursor, Antigravity IDE all ship one) — without it,
        // `start` opens a visible console window to host the batch
        // interpreter before it hands off to the real GUI process.
        $fullCommand = PHP_OS_FAMILY === 'Windows'
            ? "start /B \"\" {$command}"
            : "{$command} > /dev/null 2>&1 &";

        $nullDevice = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';

        $descriptors = [
            0 => ['file', $nullDevice, 'r'],
            1 => ['file', $nullDevice, 'w'],
            2 => ['file', $nullDevice, 'w'],
        ];

        $process = @proc_open($fullCommand, $descriptors, $pipes);

        if (! is_resource($process)) {
            return false;
        }

        proc_close($process);

        return true;
    }

    /**
     * PHP's exec()/shell_exec() child processes inherit the *calling PHP
     * process's* environment block — not a normal interactive desktop
     * session's. When Laravel is served by a background worker (e.g.
     * Herd's PHP process) rather than a shell, %USERPROFILE% can come
     * through undefined. Electron apps (VS Code) and JetBrains IDEs both
     * hard-require it at startup and crash with an uncaught exception
     * before a window ever opens if it's missing.
     *
     * Fixed via putenv() rather than prefixing the exec() command string
     * with `set VAR=val&&` — the latter reliably caused exec() to hang (see
     * the note in run()). putenv() changes this PHP process's own
     * environment, which exec()'s child inherits normally, with no command
     * string surgery involved.
     */
    private function ensureWindowsUserEnv(): void
    {
        $profile = getenv('USERPROFILE');

        if ($profile !== false && $profile !== '') {
            return;
        }

        // `whoami` queries the OS process token directly, unlike
        // get_current_user() which returns the *script file's* filesystem
        // owner — not the OS user this process actually executes as.
        $who = trim((string) $this->shell('whoami'));

        if ($who === '') {
            return;
        }

        $username = str_contains($who, '\\') ? substr($who, strrpos($who, '\\') + 1) : $who;
        $systemDrive = getenv('SystemDrive') ?: 'C:';

        putenv("USERPROFILE={$systemDrive}\\Users\\{$username}");
    }

    private function shell(string $command): ?string
    {
        if (! function_exists('shell_exec')) {
            return null;
        }

        return shell_exec($command);
    }
}
