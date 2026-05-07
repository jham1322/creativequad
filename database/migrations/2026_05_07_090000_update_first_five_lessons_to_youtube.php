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
            1 => 'https://youtu.be/B06i_Nq4Tds',
            2 => 'https://youtu.be/p5P3vOpVp_A',
            3 => 'https://youtu.be/xQkB9pvt9kY',
            4 => 'https://youtu.be/330LFa9p1qw',
            5 => 'https://youtu.be/V4Gh8sXSBgA',
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
            ->whereBetween('module_number', [1, 5])
            ->update([
                'video_url' => $videoUrl,
                'updated_at' => now(),
            ]);
    }
};
