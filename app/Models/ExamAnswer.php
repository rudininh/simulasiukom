<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = ['exam_attempt_id', 'question_id', 'selected_answer', 'is_correct', 'score_obtained', 'answered_at'];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
