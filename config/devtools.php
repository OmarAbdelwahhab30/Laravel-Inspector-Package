<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Env Var
    |--------------------------------------------------------------------------
    |
    | The env var name checked by Support\Enabled (in addition to APP_DEBUG
    | and APP_ENV=local) before the package registers anything. Change this
    | if LARAVEL_DEVTOOLS_ENABLED collides with another env var in your app.
    |
    */
    'enabled_env_var' => 'LARAVEL_DEVTOOLS_ENABLED',

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Only 'file' is currently supported; any other value throws at boot.
    |
    */
    'storage' => [
        'driver' => 'file',
        'path' => storage_path('devtools'),
        'prune_after_minutes' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Paths
    |--------------------------------------------------------------------------
    |
    | Paths that should not be recorded. Supports wildcards.
    |
    */
    'ignore_paths' => [
        '__devtools',
        '__devtools/*',
        '_debugbar/*',
        'storage/*',
        'assets/*',
        'ajax/*',
        'favicon.ico',
        'build/*',
        'dashboard/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Collectors
    |--------------------------------------------------------------------------
    |
    | Each future collector (query, cache, event, job, log, exception, ...)
    | adds one entry here plus one Collectors/XCollector.php file — no
    | changes to the provider, recorder, or existing collectors.
    |
    */
    'collectors' => [
        'route' => true,
        'controller' => true,
        'response' => true,
        'query' => true,
        'event' => true,
        'job' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Query Threshold
    |--------------------------------------------------------------------------
    |
    | Queries taking longer than this threshold (in milliseconds) will be
    | flagged as slow in the dashboard.
    |
    */
    'slow_query_threshold' => 50,

    /*
    |--------------------------------------------------------------------------
    | Redacted Bindings
    |--------------------------------------------------------------------------
    |
    | Bindings whose column name matches one of these patterns (checked
    | against the column name immediately preceding the placeholder in the
    | SQL) are stored as "[REDACTED]" instead of their real value, since
    | snapshots are persisted as plaintext JSON on disk.
    |
    */
    'redact_bindings' => [
        '*password*',
        '*passwd*',
        '*secret*',
        '*token*',
        '*api_key*',
        '*apikey*',
        '*access_key*',
        '*private_key*',
        '*credit_card*',
        '*card_number*',
        '*cvv*',
        '*ssn*',
    ],

];
