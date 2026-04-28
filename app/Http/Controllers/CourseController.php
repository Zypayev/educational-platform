<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class CourseController extends Controller
{
    //
    public function index()
    {
        // We "Eager Load" the lessons so the array isn't empty in Vue
        $courses = \App\Models\Course::with(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }])
            ->withCount('lessons')
            ->get();

        return inertia('Courses/Index', [
            'courses' => $courses
        ]);
    }
}
