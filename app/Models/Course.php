<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Import the trait
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory; // 2. Use the trait inside the class

    // Optional: Add fillable if you want to allow mass assignment
    protected $fillable = ['title', 'slug', 'description', 'thumbnail', 'user_id', 'price'];

    public function lessons()
    {
        // This tells Laravel that one course has many lessons
        return $this->hasMany(Lesson::class)->orderBy('position');
    }
}
