<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $table = 'project_question_options';
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
