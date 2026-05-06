<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

        if (Str::contains($this->video_url, '/preview')) {
            return $this->video_url;
        }

        if (preg_match('#/file/d/([^/]+)#', $this->video_url, $matches) === 1) {
            return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        }

        return $this->video_url;
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
