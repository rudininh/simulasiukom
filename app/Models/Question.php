<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id', 'exam_category_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer', 'score', 'order_number', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }
}
