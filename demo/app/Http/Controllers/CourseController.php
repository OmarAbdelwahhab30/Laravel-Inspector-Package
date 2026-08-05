<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function show(int $course): JsonResponse
    {
        if ($course === 0) {
            throw new \RuntimeException('Course id cannot be zero.');
        }

        return response()->json([
            'id' => $course,
            'title' => 'Laravel for Everyone',
            'author' => 'Omar Abdulwahhab',
        ]);
    }
}
