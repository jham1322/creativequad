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

        DB::table('lessons')->updateOrInsert(
            ['module_number' => 11],
            [
                'title' => 'Important: Common Student Mistakes, Problems, and How to Avoid Them',
                'description' => 'One of the most important lessons in the dashboard. Watch this carefully so you can avoid the common mistakes, confusion, and workflow problems that usually slow students down while building and deploying.',
                'duration' => 'Must watch',
                'status' => 'queued',
                'video_url' => 'https://www.youtube.com/watch?v=3ogUSl7HfaE',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        DB::table('lessons')
            ->where('module_number', 11)
            ->delete();
    }
};
