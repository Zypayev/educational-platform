<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Comment;

class CommentController extends Controller
{
    //
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
