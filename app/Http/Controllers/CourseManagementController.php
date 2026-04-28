<?php

namespace App\Http\Controllers;

use Illuminate\Facades\Storage;
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
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        return inertia('Courses/Edit', [
            'course' => $course,
            'lessons' => $course->lessons()->orderBy('position')->get()->map(fn($l) => [
                'id'        => $l->id,
                'title'     => $l->title,
                'slug'      => $l->slug,
                'content'   => $l->content,
                'video_url' => $l->video_url,
                'position'  => (int) $l->position,
            ]),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $fields = $request->validate([
            'title'       => 'required|max:255|unique:courses,title,' . $course->id,
            'description' => 'required',
            'price'       => 'nullable|numeric|min:0',
            'thumbnail'   => 'nullable|image|max:2048',
        ]);

        $data = [
            'title'       => $fields['title'],
            'slug'        => \Illuminate\Support\Str::slug($fields['title']),
            'description' => $fields['description'],
            'price'       => $fields['price'] ?? $course->price,
        ];

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if it exists
            if ($course->thumbnail) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($data);

        return back();
    }

    public function updateLesson(Request $request, \App\Models\Lesson $lesson)
    {
        // Only the course owner can edit its lessons
        if ($lesson->course->user_id !== auth()->id()) {
            abort(403);
        }

        $fields = $request->validate([
            'title'     => 'required|max:255',
            'content'   => 'required',
            'video_url' => 'nullable|url',
            'position'  => 'required|integer|min:1',
        ]);

        $lesson->update([
            'title'     => $fields['title'],
            'slug'      => \Illuminate\Support\Str::slug($fields['title']),
            'content'   => $fields['content'],
            'video_url' => $fields['video_url'],
            'position'  => $fields['position'],
        ]);

        return back();
    }

    public function destroyLesson(\App\Models\Lesson $lesson)
    {
        if ($lesson->course->user_id !== auth()->id()) {
            abort(403);
        }

        $lesson->delete();

        return back();
    }
}
