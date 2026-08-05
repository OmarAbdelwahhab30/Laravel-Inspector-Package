<?php

namespace OmarAbdulwahhab\LaravelInspector\Support;

class Enabled
{
    /**
     * Package is active only when all three hold: explicit opt-in env flag,
     * APP_DEBUG=true, and APP_ENV=local. This is checked once in the service
     * provider's register() so that when false, nothing is registered at all
     * (no routes, no listeners, no middleware) rather than merely guarded at runtime.
     */
    public static function check(): bool
    {
        // The config is merged before this runs (see the service provider),
        // so devtools.enabled_env_var can rename the opt-in flag; env() still
        // reads straight from the environment since config() would just be
        // relaying the same env value for this particular key.
        $envVar = config('devtools.enabled_env_var', 'LARAVEL_DEVTOOLS_ENABLED');

        return (bool) env($envVar, false)
            && (bool) config('app.debug')
            && app()->environment('local');
    }
}
