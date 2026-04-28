<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // For clean URLs like /courses/learn-php
            $table->text('description');
            $table->string('thumbnail')->nullable(); // Image path
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Instructor
            $table->decimal('price', 8, 2)->default(0); // For that future Stripe integration!
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
