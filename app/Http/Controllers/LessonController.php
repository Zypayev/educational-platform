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
        // Check if the lesson actually belongs to this course
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        return Inertia::render('Lessons/Show', [
            'course' => $course,
            'lesson' => $lesson,
            'allLessons' => $course->lessons()->orderBy('position')->get(),
            // Check if the current user has completed this specific lesson
            'isCompleted' => auth()->user()->completedLessons()->where('lesson_id', $lesson->id)->exists(),
            'comments' => $lesson->comments()->with('user:id,name')->get(),
        ]);
    }

    public function toggleComplete(Request $request, Lesson $lesson)
    {
        // toggle() adds the record if it's missing, and removes it if it's already there
        $request->user()->completedLessons()->toggle($lesson->id);

        return back(); // Inertia will refresh the data automatically
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
