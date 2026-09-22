<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolEvent extends Model
{
    protected $table = 'tool_events';
    
    public $timestamps = false;
    
    protected $fillable = [
        'tool_slug',
        'event_type',
        'session_key',
        'sponsor_key',
        'target_url',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}
