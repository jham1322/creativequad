<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_number',
        'title',
        'description',
        'duration',
        'status',
        'video_url',
    ];

    protected $appends = [
        'embed_video_url',
        'lesson_number',
        'status_label',
        'status_badge_class',
    ];

    public function getEmbedVideoUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if ($youtubeVideoId = $this->extractYoutubeVideoId($this->video_url)) {
            return $this->buildYoutubeEmbedUrl($youtubeVideoId);
        }

        if (str_contains($this->video_url, '/preview')) {
            return $this->video_url;
        }

        if (preg_match('#/file/d/([^/]+)#', $this->video_url, $matches) === 1) {
            return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        }

        return $this->video_url;
    }

    private function extractYoutubeVideoId(string $url): ?string
    {
        if (preg_match('#youtube(?:-nocookie)?\.com/embed/([^?&/]+)#', $url, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('#youtu\.be/([^?&/]+)#', $url, $matches) === 1) {
            return $matches[1];
        }

        $parts = parse_url($url);

        if (! isset($parts['host'])) {
            return null;
        }

        if (str_contains($parts['host'], 'youtube.com')) {
            parse_str($parts['query'] ?? '', $query);

            if (! empty($query['v'])) {
                return $query['v'];
            }
        }

        return null;
    }

    private function buildYoutubeEmbedUrl(string $videoId): string
    {
        return 'https://www.youtube-nocookie.com/embed/' . $videoId . '?' . http_build_query([
            'rel' => 0,
            'playsinline' => 1,
            'iv_load_policy' => 3,
            'fs' => 0,
            'disablekb' => 1,
        ]);
    }

    public function getLessonNumberAttribute(): string
    {
        return str_pad((string) $this->module_number, 2, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'playing_now' => 'Playing now',
            'up_next' => 'Up next',
            default => 'Queued',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'playing_now' ? 'lms-lesson-tag' : 'lms-lesson-tag lms-lesson-tag-muted';
    }
}
