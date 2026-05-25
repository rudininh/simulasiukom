<?php

namespace App\Jobs;

use App\Models\Regulation;
use App\Services\RegulationOcrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessRegulationOcrJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $regulationId)
    {
    }

    public function handle(RegulationOcrService $service): void
    {
        $service->process(Regulation::findOrFail($this->regulationId));
    }
}
