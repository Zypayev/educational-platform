<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseManagementController extends Controller
{
    //
    public function create()
    {
        return inertia('Courses/Create');
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'required|image|max:2048',
        ]);

        $path = $request->file('thumbnail')->store('thumbnails', 'public');

        $course = Course::create([
            'title' => $fields['title'],
            'slug' => \Illuminate\Support\Str::slug($fields['title']),
            'description' => $fields['description'],
            'thumbnail' => $path,
            'user_id' => auth()->id(),
        ]);

        // Redirect to the "Add Lessons" page for this new course
        return redirect()->route('courses.lessons.create', $course->id);
    }

    public function addLessons(Course $course)
    {
        return inertia('Courses/AddLessons', [
            'course' => $course,
            'lessons' => $course->lessons // Show the list of lessons as we add them
        ]);
    }

    public function storeLesson(Request $request, Course $course)
    {
        $fields = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'video_url' => 'nullable|url'
        ]);

        $course->lessons()->create([
            'title' => $fields['title'],
            'slug' => \Illuminate\Support\Str::slug($fields['title']),
            'content' => $fields['content'],
            'video_url' => $fields['video_url'],
            'position' => $course->lessons()->count() + 1
        ]);

        return back(); // This keeps us on the same page to add more!
    }

    public function edit(Course $course)
    {
        // Security: Only the owner can edit
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        return inertia('Courses/Edit', ['course' => $course]);
    }

    public function update(Request $request, Course $course)
    {
        // Basic Ownership Check
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $fields = $request->validate([
            'title' => 'required|max:255|unique:courses,title,' . $course->id,
            'description' => 'required',
        ]);

        $course->update([
            'title' => $fields['title'],
            'slug' => \Illuminate\Support\Str::slug($fields['title']),
            'description' => $fields['description'],
        ]);

        return redirect()->route('courses.index');
    }
}
