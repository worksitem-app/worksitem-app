<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'project_questions';
    protected $guarded = [];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('sort_order');
    }
}
