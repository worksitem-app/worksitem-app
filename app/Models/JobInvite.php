<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobInvite extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['viewed_at' => 'datetime'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobRequest::class, 'job_request_id');
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }
}
