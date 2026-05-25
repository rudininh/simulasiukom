<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedQuestion extends Model
{
    protected $fillable = [
        'regulation_id', 'exam_id', 'exam_category_id', 'question_text', 'option_a', 'option_b',
        'option_c', 'option_d', 'option_e', 'correct_answer', 'explanation', 'source_reference',
        'source_page', 'source_chunk_index', 'difficulty', 'question_type', 'validation_status',
        'validation_notes', 'ai_model', 'ai_raw_response', 'status', 'created_by',
    ];

    public function regulation()
    {
        return $this->belongsTo(Regulation::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }
}
