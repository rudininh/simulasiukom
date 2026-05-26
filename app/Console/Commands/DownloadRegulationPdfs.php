<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationPdfDownloaderService;
use Illuminate\Console\Command;

class DownloadRegulationPdfs extends Command
{
    protected $signature = 'regulation:download-pdfs';

    protected $description = 'Download regulation PDFs from configured official PDF URLs';

    public function handle(RegulationPdfDownloaderService $downloader): int
    {
        $success = 0;
        $failed = 0;

        Regulation::whereNotNull('pdf_url')->where('pdf_url', '<>', '')->orderBy('id')->each(function (Regulation $regulation) use ($downloader, &$success, &$failed) {
            $this->line('Download: '.$regulation->title);
            $downloader->download($regulation) ? $success++ : $failed++;
        });

        $manual = Regulation::where(function ($query) {
            $query->whereNull('pdf_url')->orWhere('pdf_url', '');
        })->update(['download_status' => 'manual_required']);

        $this->info("PDF berhasil: {$success}");
        $this->warn("PDF gagal: {$failed}");
        $this->line("Perlu upload manual: {$manual}");

        return self::SUCCESS;
    }
}
