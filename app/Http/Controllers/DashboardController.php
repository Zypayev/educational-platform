<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Get courses the user has interacted with (started)
        $courses = \App\Models\Course::with(['lessons' => function ($q) {
            $q->orderBy('position', 'asc')->limit(1); // Just get the first lesson for the link
        }])
            ->withCount('lessons')
            ->whereHas('lessons') // only courses with content
            ->get()
            ->map(function ($course) use ($user) {
                // Count how many lessons in THIS course the user has finished
                $completedCount = $user->completedLessons()
                    ->where('course_id', $course->id)
                    ->count();

                // Calculate percentage
                $course->progress_percent = $course->lessons_count > 0
                    ? round(($completedCount / $course->lessons_count) * 100)
                    : 0;

                return $course;
            });

        return Inertia::render('Dashboard', [
            'enrolledCourses' => $courses
        ]);
    }
}
