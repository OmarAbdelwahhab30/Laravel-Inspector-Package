<?php

namespace OmarAbdulwahhab\LaravelInspector;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\ServiceProvider;
use OmarAbdulwahhab\LaravelInspector\Console\PruneCommand;
use OmarAbdulwahhab\LaravelInspector\Contracts\StorageDriver;
use OmarAbdulwahhab\LaravelInspector\Middleware\AssignRequestId;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;
use OmarAbdulwahhab\LaravelInspector\Services\SnapshotBuilder;
use OmarAbdulwahhab\LaravelInspector\Storage\FileStorageDriver;
use OmarAbdulwahhab\LaravelInspector\Support\Enabled;

class LaravelInspectorServiceProvider extends ServiceProvider
{
    private const HEADER = 'X-Laravel-Devtools-Request';

    /**
     * Bails out immediately when the three-flag gate fails: no config merge
     * beyond the check itself, no bindings. Keeps the package at zero
     * overhead and zero attack surface outside of local dev.
     */
    public function register(): void
    {
        // Config must be merged before the gate check so that a published
        // devtools.enabled_env_var override is honored — this merge alone is
        // cheap and side-effect-free, so doing it unconditionally is safe.
        $this->mergeConfigFrom(__DIR__.'/../config/devtools.php', 'devtools');

        if (! Enabled::check()) {
            return;
        }

        $this->app->singleton(StorageDriver::class, function () {
            return $this->makeStorageDriver();
        });

        $this->app->scoped(Recorder::class);
    }

    private function makeStorageDriver(): StorageDriver
    {
        $driver = config('devtools.storage.driver', 'file');

        return match ($driver) {
            'file' => new FileStorageDriver(config('devtools.storage.path')),
            default => throw new \InvalidArgumentException(
                "Unsupported devtools storage driver [{$driver}]. Only 'file' is currently supported."
            ),
        };
    }

    public function boot(): void
    {
        if (! Enabled::check()) {
            return;
        }

        $this->prependRequestIdMiddleware();

        // Boot Recording Collectors
        $collectors = config('devtools.collectors', []);
        $recordingCollectors = [
            'query' => \OmarAbdulwahhab\LaravelInspector\Collectors\QueryCollector::class,
            'event' => \OmarAbdulwahhab\LaravelInspector\Collectors\EventCollector::class,
            'job' => \OmarAbdulwahhab\LaravelInspector\Collectors\JobCollector::class,
        ];

        foreach ($recordingCollectors as $key => $class) {
            if ($collectors[$key] ?? false) {
                $this->app->make($class)->register();
            }
        }

        $this->registerRequestHandledListener();
        $this->registerTerminatingCallback();
        $this->loadRoutesFrom(__DIR__.'/../routes/devtools.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-inspector');

        if ($this->app->runningInConsole()) {
            $this->commands([PruneCommand::class]);

            $this->publishes([
                __DIR__.'/../config/devtools.php' => config_path('devtools.php'),
            ], 'devtools-config');
        }
    }

    private function prependRequestIdMiddleware(): void
    {
        $this->app->make(Kernel::class)->prependMiddleware(AssignRequestId::class);
    }

    /**
     * Fires after both the success path and the exception-render path, with
     * both $request and the final $response available — unlike code placed
     * after $next($request) in middleware, which never runs for a request
     * that threw, since the HTTP kernel renders exceptions outside the
     * middleware pipeline.
     */
    private function registerRequestHandledListener(): void
    {
        $this->app['events']->listen(RequestHandled::class, function (RequestHandled $event) {
            $recorder = $this->app->make(Recorder::class);

            if (! $recorder->hasId()) {
                return;
            }

            $event->response->headers->set(self::HEADER, $recorder->getId());

            $snapshot = $this->app->make(SnapshotBuilder::class)->build($event->request, $event->response);

            $recorder->setSnapshot($snapshot);
        });
    }

    /**
     * Runs after the response has already been sent to the browser, keeping
     * file I/O off the critical path of the user-visible response.
     */
    private function registerTerminatingCallback(): void
    {
        $this->app->terminating(function () {
            $recorder = $this->app->make(Recorder::class);
            $snapshot = $recorder->getSnapshot();

            if ($snapshot === null) {
                return;
            }

            $storage = $this->app->make(StorageDriver::class);
            $storage->put($recorder->getId(), $snapshot);

            // Automatically sweep old snapshots ~10% of the time
            if (random_int(1, 100) <= 10) {
                $storage->prune(config('devtools.storage.prune_after_minutes', 20));
            }
        });
    }
}
