<?php

use Illuminate\Support\Facades\Route;
use OmarAbdulwahhab\LaravelInspector\Http\DevtoolsController;
use OmarAbdulwahhab\LaravelInspector\Http\OpenEditorController;
use OmarAbdulwahhab\LaravelInspector\Middleware\AllowExtensionOrigin;

Route::middleware(AllowExtensionOrigin::class)->group(function () {
    Route::get('/__devtools/request/{id}', [DevtoolsController::class, 'show'])
        ->name('devtools.request.show');

    Route::get('/__devtools/open-editor', [OpenEditorController::class, 'open'])
        ->name('devtools.open-editor');
});

// The web dashboard is accessible without the AllowExtensionOrigin middleware
Route::get('/__devtools', [DevtoolsController::class, 'dashboard'])
    ->name('devtools.dashboard');

Route::get('/__devtools/latest', [DevtoolsController::class, 'latest'])
    ->name('devtools.latest');
