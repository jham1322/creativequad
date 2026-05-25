<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_key',
        'session_id',
        'user_id',
        'landing_path',
        'landing_route_name',
        'last_path',
        'last_route_name',
        'ip_hash',
        'user_agent',
        'referrer',
        'page_views',
        'first_seen_at',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'page_views' => 'integer',
            'user_id' => 'integer',
        ];
    }
}
