<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationTextExtractorService;
use Illuminate\Console\Command;

class ExtractAllRegulations extends Command
{
    protected $signature = 'regulation:extract-all';

    protected $description = 'Extract text from all available regulation files';

    public function handle(RegulationTextExtractorService $extractor): int
    {
        $ok = 0;
        $failed = 0;

        Regulation::whereNotNull('file_path')->orderBy('id')->each(function (Regulation $regulation) use ($extractor, &$ok, &$failed) {
            try {
                $extractor->extract($regulation);
                $ok++;
                $this->line('Extracted: '.$regulation->title);
            } catch (\Throwable $e) {
                $failed++;
                $this->error('Failed: '.$regulation->title.' - '.$e->getMessage());
            }
        });

        $this->info("Extract berhasil: {$ok}");
        $this->warn("Extract gagal: {$failed}");

        return self::SUCCESS;
    }
}
