<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_number');
            $table->string('title');
            $table->text('description');
            $table->string('duration', 20);
            $table->string('status', 40)->default('queued');
            $table->text('video_url')->nullable();
            $table->timestamps();
        });

        $videoUrl = 'https://drive.google.com/file/d/1xnh1l7cKzR9OrcnqKzWMjw3B-drR1ceG/preview';
        $timestamp = now();

        DB::table('lessons')->insert([
            [
                'module_number' => 1,
                'title' => 'Welcome to the course and what you’ll build',
                'description' => 'A quick orientation so students understand the full system they’re about to create and how the course flow works from start to finish.',
                'duration' => '12:48',
                'status' => 'playing_now',
                'video_url' => $videoUrl,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'module_number' => 2,
                'title' => 'Set up Codex, GitHub, and your project structure',
                'description' => 'Prepare the exact environment you need so your first build is organized from day one.',
                'duration' => '18:12',
                'status' => 'up_next',
                'video_url' => $videoUrl,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'module_number' => 3,
                'title' => 'Design your UI using Google Stitch',
                'description' => 'Translate rough ideas into a cleaner interface before turning the design into a real working app.',
                'duration' => '21:30',
                'status' => 'queued',
                'video_url' => $videoUrl,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'module_number' => 4,
                'title' => 'Build the features with AI prompts',
                'description' => 'Learn how to prompt for actual features and outputs instead of just generating disconnected snippets.',
                'duration' => '26:05',
                'status' => 'queued',
                'video_url' => $videoUrl,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
