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

        $videoUrls = [
            6 => 'https://youtu.be/8Rj3IMTBdYs',
            7 => 'https://youtu.be/X1gWxh3EuzI',
            8 => 'https://youtu.be/LIMJDPP6X9I',
            9 => 'https://youtu.be/XU_FYnbN_io',
            10 => 'https://youtu.be/AtvJTAHUk2c',
        ];

        foreach ($videoUrls as $moduleNumber => $videoUrl) {
            DB::table('lessons')
                ->where('module_number', $moduleNumber)
                ->update([
                    'video_url' => $videoUrl,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        $videoUrl = 'https://drive.google.com/file/d/1xnh1l7cKzR9OrcnqKzWMjw3B-drR1ceG/preview';

        DB::table('lessons')
            ->whereBetween('module_number', [6, 10])
            ->update([
                'video_url' => $videoUrl,
                'updated_at' => now(),
            ]);
    }
};
