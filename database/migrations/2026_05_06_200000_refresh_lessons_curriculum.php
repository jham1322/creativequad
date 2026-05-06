<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        $timestamp = now();
        $videoUrl = 'https://drive.google.com/file/d/1xnh1l7cKzR9OrcnqKzWMjw3B-drR1ceG/preview';

        $lessons = [
            [
                'module_number' => 1,
                'title' => 'Setting Up Codex, Laravel, and Your Workspace',
                'description' => 'Prepare your environment and install everything needed for the project build.',
                'duration' => 'Module 01',
                'status' => 'playing_now',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 2,
                'title' => 'Understanding How Codex Works',
                'description' => 'Learn how Codex helps you build real applications faster using prompts and structured workflows.',
                'duration' => 'Module 02',
                'status' => 'up_next',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 3,
                'title' => 'Creating the Website Structure (Part 1)',
                'description' => 'Start building the core pages and layout of the e-commerce web app.',
                'duration' => 'Module 03',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 4,
                'title' => 'Creating the Website Structure (Part 2)',
                'description' => 'Continue expanding the frontend structure and organize the user interface properly.',
                'duration' => 'Module 04',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 5,
                'title' => 'Adding Features and Improving the App (Part 1)',
                'description' => 'Implement important app functionality and begin refining the experience.',
                'duration' => 'Module 05',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 6,
                'title' => 'Adding Features and Improving the App (Part 2)',
                'description' => 'Continue building interactive features and fixing workflow issues.',
                'duration' => 'Module 06',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 7,
                'title' => 'Adding Features and Improving the App (Part 3)',
                'description' => 'Finalize the main functionality and optimize the application structure.',
                'duration' => 'Module 07',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 8,
                'title' => 'Connecting Your Project to GitHub',
                'description' => 'Push your project to GitHub and prepare your app for deployment and version control.',
                'duration' => 'Module 08',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 9,
                'title' => 'Deploying Your Laravel App to DreamHost',
                'description' => 'Upload, configure, and launch your live Laravel web application on DreamHost.',
                'duration' => 'Module 09',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
            [
                'module_number' => 10,
                'title' => 'Updating Your Live Website Through Codex → GitHub → Server',
                'description' => 'Learn the real workflow of updating a live production app using Codex, GitHub, and your hosting server without rebuilding from scratch.',
                'duration' => 'Module 10',
                'status' => 'queued',
                'video_url' => $videoUrl,
            ],
        ];

        DB::table('lessons')
            ->whereNotIn('module_number', array_column($lessons, 'module_number'))
            ->delete();

        foreach ($lessons as $lesson) {
            DB::table('lessons')->updateOrInsert(
                ['module_number' => $lesson['module_number']],
                array_merge($lesson, ['updated_at' => $timestamp, 'created_at' => $timestamp]),
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        DB::table('lessons')
            ->whereBetween('module_number', [1, 10])
            ->delete();
    }
};
