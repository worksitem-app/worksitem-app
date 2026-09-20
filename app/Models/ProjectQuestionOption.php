<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectQuestionOption extends Model
{
    use HasFactory;

    protected $table = 'project_question_options';

    protected $fillable = [
        'question_id',
        'label',
        'value',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProjectQuestion::class, 'question_id');
    }
}
