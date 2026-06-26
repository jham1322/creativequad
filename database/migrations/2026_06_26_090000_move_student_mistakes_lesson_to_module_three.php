<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STUDENT_MISTAKES_TITLE = 'Important: Common Student Mistakes, Problems, and How to Avoid Them';

    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        DB::transaction(function (): void {
            $lesson = DB::table('lessons')
                ->where('title', self::STUDENT_MISTAKES_TITLE)
                ->first();

            if (! $lesson) {
                return;
            }

            DB::table('lessons')
                ->where('id', $lesson->id)
                ->update([
                    'module_number' => 999,
                    'updated_at' => now(),
                ]);

            DB::table('lessons')
                ->whereBetween('module_number', [3, 10])
                ->increment('module_number');

            DB::table('lessons')
                ->where('id', $lesson->id)
                ->update([
                    'module_number' => 3,
                    'duration' => 'Module 03',
                    'status' => 'queued',
                    'updated_at' => now(),
                ]);

            DB::table('lessons')
                ->where('module_number', '>', 3)
                ->orderBy('module_number')
                ->get(['id', 'module_number'])
                ->each(function ($lesson): void {
                    DB::table('lessons')
                        ->where('id', $lesson->id)
                        ->update([
                            'duration' => 'Module ' . str_pad((string) $lesson->module_number, 2, '0', STR_PAD_LEFT),
                            'updated_at' => now(),
                        ]);
                });
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        DB::transaction(function (): void {
            $lesson = DB::table('lessons')
                ->where('title', self::STUDENT_MISTAKES_TITLE)
                ->first();

            if (! $lesson) {
                return;
            }

            DB::table('lessons')
                ->where('id', $lesson->id)
                ->update([
                    'module_number' => 999,
                    'updated_at' => now(),
                ]);

            DB::table('lessons')
                ->whereBetween('module_number', [4, 11])
                ->decrement('module_number');

            DB::table('lessons')
                ->where('id', $lesson->id)
                ->update([
                    'module_number' => 11,
                    'duration' => 'Must watch',
                    'status' => 'queued',
                    'updated_at' => now(),
                ]);

            DB::table('lessons')
                ->whereBetween('module_number', [3, 10])
                ->orderBy('module_number')
                ->get(['id', 'module_number'])
                ->each(function ($lesson): void {
                    DB::table('lessons')
                        ->where('id', $lesson->id)
                        ->update([
                            'duration' => 'Module ' . str_pad((string) $lesson->module_number, 2, '0', STR_PAD_LEFT),
                            'updated_at' => now(),
                        ]);
                });
        });
    }
};
