<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/courses/{course}', [CourseController::class, 'show'])
    ->name('courses.show');
