<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Inertia\Inertia;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = auth()->user();
        $completedIds = $user->completedLessons()->pluck('lesson_id');

        return Inertia::render('Lessons/Show', [
            'course' => $course,
            'lesson' => $lesson,
            'allLessons' => $course->lessons()
                ->orderBy('position')
                ->get()
                ->map(fn($l) => [
                    'id' => $l->id,
                    'title' => $l->title,
                    'slug' => $l->slug,
                    'is_done' => $completedIds->contains($l->id),
                ]),
            'isCompleted' => $completedIds->contains($lesson->id),
            'comments' => $lesson->comments()->with('user:id,name')->get(),
        ]);
    }

    public function toggleComplete(Request $request, Lesson $lesson)
    {
        $request->user()->completedLessons()->toggle($lesson->id);

        return redirect()->back();
    }

    public function storeComment(Request $request, Lesson $lesson)
    {
        $request->validate([
            'body' => 'required|min:3|max:1000'
        ]);

        $lesson->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return back(); // Inertia will refresh the comments list automatically
    }
}
