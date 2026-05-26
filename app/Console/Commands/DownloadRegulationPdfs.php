<?php

namespace App\Console\Commands;

use App\Models\Regulation;
use App\Services\RegulationPdfDownloaderService;
use Illuminate\Console\Command;

class DownloadRegulationPdfs extends Command
{
    protected $signature = 'regulation:download-pdfs';

    protected $description = 'Download regulation PDFs from configured official/detail URLs';

    public function handle(RegulationPdfDownloaderService $downloader): int
    {
        $success = 0;
        $failed = 0;

        $errors = [];
        $processed = 0;

        Regulation::where('download_status', '<>', 'downloaded')
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('official_url')->where('official_url', '<>', '');
                })->orWhere(function ($subQuery) {
                    $subQuery->whereNotNull('pdf_url')->where('pdf_url', '<>', '');
                });
            })
            ->orderBy('id')
            ->each(function (Regulation $regulation) use ($downloader, &$success, &$failed, &$processed, &$errors) {
            $processed++;
            $this->line('Download: '.$regulation->title);
            $result = $downloader->download($regulation);
            if ($result['success']) {
                $success++;
                $this->info('  OK: '.$result['file_path']);
            } else {
                $failed++;
                $errors[] = $regulation->title.' - '.$result['message'];
                $this->warn('  Gagal: '.$result['message']);
            }
        });

        $manual = Regulation::where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->whereNull('pdf_url')->orWhere('pdf_url', '');
            })->where(function ($subQuery) {
                $subQuery->whereNull('official_url')->orWhere('official_url', '');
            });
        })->update(['download_status' => 'manual_required']);

        $this->line("Diproses: {$processed}");
        $this->info("PDF berhasil: {$success}");
        $this->warn("PDF gagal: {$failed}");
        $this->line("Perlu upload manual: {$manual}");
        foreach ($errors as $error) {
            $this->line('- '.$error);
        }

        return self::SUCCESS;
    }
}
