<?php

namespace App\Services;

use App\Models\Regulation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegulationPdfDownloaderService
{
    public function download(Regulation $regulation): bool
    {
        if (!$regulation->pdf_url) {
            $regulation->update([
                'download_status' => 'manual_required',
                'download_error' => 'URL PDF resmi belum tersedia. Silakan upload manual dari admin.',
            ]);

            return false;
        }

        $regulation->update(['download_status' => 'pending', 'download_error' => null]);

        try {
            $response = Http::timeout(90)
                ->withHeaders(['User-Agent' => 'SIMULASI-CAT-ASN/1.0'])
                ->get($regulation->pdf_url);

            if (!$response->successful()) {
                throw new \RuntimeException('HTTP '.$response->status().' saat download PDF.');
            }

            $body = $response->body();
            $contentType = strtolower($response->header('Content-Type', 'application/pdf'));
            if (!str_contains($contentType, 'pdf') && !str_starts_with(ltrim($body), '%PDF')) {
                throw new \RuntimeException('Response tidak terdeteksi sebagai PDF.');
            }

            $filename = $this->filename($regulation);
            $path = 'regulations/'.$filename;
            Storage::disk('public')->put($path, $body);

            $regulation->update([
                'file_path' => $path,
                'original_filename' => $filename,
                'mime_type' => 'application/pdf',
                'file_size' => strlen($body),
                'download_status' => 'downloaded',
                'download_error' => null,
                'downloaded_at' => now(),
                'status' => 'active',
            ]);

            return true;
        } catch (\Throwable $e) {
            $regulation->update([
                'download_status' => 'failed',
                'download_error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function filename(Regulation $regulation): string
    {
        $base = Str::slug(($regulation->regulation_number ?: $regulation->title).'-'.($regulation->year ?: 'asn'));
        return trim($base, '-') . '.pdf';
    }
}
