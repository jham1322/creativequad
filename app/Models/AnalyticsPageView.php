<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsPageView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visitor_key',
        'session_id',
        'user_id',
        'path',
        'route_name',
        'referrer',
        'ip_hash',
        'user_agent',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'user_id' => 'integer',
        ];
    }
}
