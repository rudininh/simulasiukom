<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = ['user_id', 'exam_id', 'started_at', 'finished_at', 'duration_seconds', 'score_total', 'score_regulasi_asn', 'score_manajemen_asn', 'score_kepemimpinan', 'score_pelayanan_publik', 'score_studi_kasus', 'total_answered', 'total_correct', 'total_wrong', 'status', 'competency_status'];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function remainingSeconds(): int
    {
        if ($this->status !== 'ongoing') {
            return 0;
        }

        $endsAt = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
        return max(0, now()->diffInSeconds($endsAt, false));
    }

    public function answeredCount(): int
    {
        return $this->answers()->whereNotNull('selected_answer')->count();
    }
}
