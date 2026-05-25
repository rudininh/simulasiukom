<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCategory extends Model
{
    protected $fillable = ['exam_id', 'name', 'code', 'question_count', 'passing_score'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
