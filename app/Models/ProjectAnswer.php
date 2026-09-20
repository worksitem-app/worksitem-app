<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'question_id',
        'option_id',
        'answer_value',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProjectQuestion::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(
            ProjectQuestionOption::class,
            'option_id'
        );
    }
}
