<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Regulation;
use App\Services\QuestionGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateQuestionsFromRegulationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $regulationId,
        public int $examId,
        public int $categoryId,
        public int $count,
        public string $difficulty,
        public string $questionType,
        public int $userId,
        public ?string $keywords = null,
    ) {
    }

    public function handle(QuestionGeneratorService $service): void
    {
        $regulation = Regulation::findOrFail($this->regulationId);
        $service->generate(
            $regulation,
            Exam::findOrFail($this->examId),
            ExamCategory::findOrFail($this->categoryId),
            $this->count,
            $this->difficulty,
            $this->questionType,
            $this->userId,
            $regulation->extracted_text,
            $this->keywords,
        );
    }
}
