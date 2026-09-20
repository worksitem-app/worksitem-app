<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_question_id',
        'source_option_id',
        'target_question_id',
        'action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sourceQuestion(): BelongsTo
    {
        return $this->belongsTo(ProjectQuestion::class, 'source_question_id');
    }

    public function sourceOption(): BelongsTo
    {
        return $this->belongsTo(
            ProjectQuestionOption::class,
            'source_option_id'
        );
    }

    public function targetQuestion(): BelongsTo
    {
        return $this->belongsTo(ProjectQuestion::class, 'target_question_id');
    }
}
