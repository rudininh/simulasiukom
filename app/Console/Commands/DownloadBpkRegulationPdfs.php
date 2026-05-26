<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationPdfDownloaderService;
use Illuminate\Console\Command;

class DownloadBpkRegulationPdfs extends Command
{
    protected $signature = 'regulation:download-bpk';

    protected $description = 'Download regulation PDFs from peraturan.bpk.go.id detail pages';

    public function handle(RegulationPdfDownloaderService $downloader): int
    {
        $success = 0;
        $failed = 0;

        Regulation::where('official_url', 'like', '%peraturan.bpk.go.id%')
            ->where('download_status', '<>', 'downloaded')
            ->orderBy('id')
            ->each(function (Regulation $regulation) use ($downloader, &$success, &$failed) {
                $this->line('BPK: '.$regulation->title);
                $result = $downloader->download($regulation);
                if ($result['success']) {
                    $success++;
                    $this->info('  OK: '.$result['file_path']);
                } else {
                    $failed++;
                    $this->warn('  Gagal: '.$result['message']);
                }
            });

        $this->info("BPK berhasil: {$success}");
        $this->warn("BPK gagal: {$failed}");

        return self::SUCCESS;
    }
}
