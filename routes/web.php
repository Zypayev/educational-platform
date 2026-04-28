<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =============================================================
// 1. PUBLIC ROUTES (Accessible by everyone)
// =============================================================
Route::get('/', [CourseController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');


// =============================================================
// 2. AUTHENTICATED ROUTES (Any logged-in student)
// =============================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show');

    // Progress Tracking
    Route::post('/lessons/{lesson:slug}/complete', [LessonController::class, 'toggleComplete'])
        ->name('lessons.complete');
    Route::post('/lessons/{lesson}/comments', [LessonController::class, 'storeComment'])->name('comments.store');

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =============================================================
    // 3. INSTRUCTOR ONLY ROUTES (Locked by Middleware)
    // =============================================================
    Route::middleware([\App\Http\Middleware\EnsureUserIsInstructor::class])->group(function () {

        // Course Management
        Route::get('/instructor/courses/create', [CourseManagementController::class, 'create'])->name('courses.create');
        Route::post('/instructor/courses', [CourseManagementController::class, 'store'])->name('courses.store');
        Route::get('/instructor/courses/{course}/edit', [CourseManagementController::class, 'edit'])->name('courses.edit');
        Route::put('/instructor/courses/{course}', [CourseManagementController::class, 'update'])->name('courses.update');

        // Lesson Management
        Route::get('/instructor/courses/{course}/lessons/create', [CourseManagementController::class, 'addLessons'])->name('courses.lessons.create');
        Route::post('/instructor/courses/{course}/lessons', [CourseManagementController::class, 'storeLesson'])->name('courses.lessons.store');
    });
});

// Include Breeze Auth Routes (Only once!)
require __DIR__ . '/auth.php';
