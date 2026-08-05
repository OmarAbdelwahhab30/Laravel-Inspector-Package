<?php

namespace OmarAbdulwahhab\LaravelInspector\Console;

use Illuminate\Console\Command;
use OmarAbdulwahhab\LaravelInspector\Contracts\StorageDriver;

/**
 * Not auto-registered into the host app's scheduler — only registered as an
 * artisan command. Scheduling it (e.g. via $schedule->command('devtools:prune'))
 * is left to the consuming application to avoid surprising side effects in
 * someone else's Kernel.
 */
class PruneCommand extends Command
{
    protected $signature = 'devtools:prune {--older-than=60 : Delete snapshots older than this many minutes}';

    protected $description = 'Delete stored Laravel Inspector snapshots older than the given age';

    public function handle(StorageDriver $storage): int
    {
        $olderThan = (int) $this->option('older-than');

        $removed = $storage->prune($olderThan);

        $this->info("Pruned {$removed} snapshot(s) older than {$olderThan} minute(s).");

        return self::SUCCESS;
    }
}
