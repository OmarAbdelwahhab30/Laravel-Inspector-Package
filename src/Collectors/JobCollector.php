<?php

namespace OmarAbdulwahhab\LaravelInspector\Collectors;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use OmarAbdulwahhab\LaravelInspector\Contracts\RecordingCollector;
use OmarAbdulwahhab\LaravelInspector\Services\Recorder;

class JobCollector implements RecordingCollector
{
    private array $jobs = [];

    public function __construct(
        private readonly Recorder $recorder
    ) {
    }

    public function register(): void
    {
        Event::listen(\Illuminate\Queue\Events\JobQueued::class, function (\Illuminate\Queue\Events\JobQueued $event) {
            $jobClass = is_object($event->job) ? get_class($event->job) : (is_string($event->job) ? $event->job : 'Unknown Job');
            $duration = 0; // It's just queued, no processing time yet
            $offset = (microtime(true) - $this->recorder->getStartTime()) * 1000;

            $this->recorder->push('jobs', [
                'class' => $jobClass,
                'connection' => $event->connectionName,
                'queue' => $event->queue ?? 'default',
                'status' => 'queued',
                'time' => $duration,
            ]);

            $this->recorder->push('timeline', [
                'type' => 'job',
                'label' => $jobClass,
                'duration' => $duration,
                'offset' => $offset,
            ]);
        });
    }

    public function flush(): array
    {
        return [];
    }

}
