<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttemptCategoryScore extends Model
{
    protected $fillable = [
        'exam_attempt_id', 'exam_category_id', 'score', 'total_questions',
        'total_answered', 'total_correct', 'total_wrong',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }
}
