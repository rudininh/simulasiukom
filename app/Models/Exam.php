<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title', 'description', 'exam_type', 'duration_minutes', 'total_questions', 'passing_grade', 'available_from', 'available_until', 'regulation_basis', 'is_active'];

    protected $casts = [
        'available_from' => 'date',
        'available_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->hasMany(ExamCategory::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_number');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
