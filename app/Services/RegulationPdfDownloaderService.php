<?php

namespace App\Services;

use App\Models\Regulation;
use DOMDocument;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class RegulationPdfDownloaderService
{
    private const BPK_BASE_URL = 'https://peraturan.bpk.go.id';

    public function download(Regulation $regulation): array
    {
        $regulation->update(['download_status' => 'pending', 'download_error' => null]);

        try {
            $pdfUrl = $this->resolvePdfUrl($regulation);
            if (!$pdfUrl) {
                return $this->fail($regulation, 'manual_required', 'official_url/pdf_url belum tersedia');
            }

            $response = Http::retry(3, 500)
                ->timeout(60)
                ->withHeaders($this->pdfHeaders())
                ->get($pdfUrl);

            if ($response->status() !== 200) {
                return $this->fail($regulation, 'failed', 'HTTP '.$response->status().' saat download PDF', $pdfUrl);
            }

            $body = $response->body();
            $contentType = strtolower($response->header('Content-Type', 'application/pdf'));
            $looksPdf = str_contains($contentType, 'pdf')
                || str_ends_with(strtolower(parse_url($pdfUrl, PHP_URL_PATH) ?: ''), '.pdf')
                || str_starts_with(ltrim($body), '%PDF');

            if (!$looksPdf) {
                return $this->fail($regulation, 'failed', 'File yang diunduh bukan PDF', $pdfUrl);
            }

            if (strlen($body) <= 10240) {
                return $this->fail($regulation, 'failed', 'File PDF tidak valid atau terlalu kecil', $pdfUrl);
            }

            $filename = $this->filename($regulation, $pdfUrl);
            $path = 'regulations/'.($regulation->year ?: 'tanpa-tahun').'/'.$filename;

            try {
                Storage::disk('public')->put($path, $body);
            } catch (Throwable $e) {
                return $this->fail($regulation, 'failed', 'Gagal menyimpan file PDF: '.$e->getMessage(), $pdfUrl);
            }

            $regulation->update([
                'pdf_url' => $pdfUrl,
                'file_path' => $path,
                'original_filename' => $filename,
                'mime_type' => 'application/pdf',
                'file_size' => strlen($body),
                'download_status' => 'downloaded',
                'download_error' => null,
                'downloaded_at' => now(),
                'is_active' => true,
            ]);

            try {
                app(RegulationTextExtractorService::class)->extract($regulation->fresh());
            } catch (Throwable $e) {
                $regulation->fresh()->update([
                    'extraction_status' => 'failed',
                    'extraction_error' => $e->getMessage(),
                    'extracted_at' => now(),
                ]);
            }

            return [
                'success' => true,
                'message' => 'PDF regulasi berhasil diunduh.',
                'pdf_url' => $pdfUrl,
                'file_path' => $path,
            ];
        } catch (Throwable $e) {
            $status = str_contains($e->getMessage(), 'Link download PDF tidak ditemukan') ? 'manual_required' : 'failed';
            return $this->fail($regulation, $status, $e->getMessage());
        }
    }

    public function findBpkPdfDownloadUrl(string $officialUrl): ?string
    {
        $response = Http::retry(3, 500)
            ->timeout(60)
            ->withHeaders($this->htmlHeaders())
            ->get($officialUrl);

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal membuka halaman detail');
        }

        $html = $response->body();
        $candidates = $this->extractLinksWithDom($html);

        if (empty($candidates)) {
            $candidates = $this->extractLinksWithRegex($html);
        }

        $urls = collect($candidates)
            ->map(fn ($href) => html_entity_decode(trim((string) $href), ENT_QUOTES | ENT_HTML5))
            ->filter()
            ->filter(fn ($href) => str_contains($href, '/Download/') || str_contains($href, 'Download') || str_contains(strtolower($href), '.pdf'))
            ->map(fn ($href) => $this->absoluteUrl($href))
            ->unique()
            ->values();

        return $urls->first(fn ($url) => str_contains($url, '/Download/') && str_contains(strtolower($url), '.pdf'))
            ?: $urls->first(fn ($url) => str_contains($url, '/Download/'))
            ?: $urls->first(fn ($url) => str_contains(strtolower($url), '.pdf'));
    }

    private function resolvePdfUrl(Regulation $regulation): ?string
    {
        if ($regulation->pdf_url) {
            return $regulation->pdf_url;
        }

        if ($regulation->official_url && str_contains(parse_url($regulation->official_url, PHP_URL_HOST) ?: '', 'peraturan.bpk.go.id')) {
            $url = $this->findBpkPdfDownloadUrl($regulation->official_url);
            if (!$url) {
                throw new \RuntimeException('Link download PDF tidak ditemukan pada halaman detail');
            }

            return $url;
        }

        return null;
    }

    private function extractLinksWithDom(string $html): array
    {
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return [];
        }

        $links = [];
        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $links[] = $anchor->getAttribute('href');
        }

        return $links;
    }

    private function extractLinksWithRegex(string $html): array
    {
        preg_match_all('/href=["\']([^"\']*\/Download\/[^"\']+)["\']/i', $html, $matches);

        return $matches[1] ?? [];
    }

    private function absoluteUrl(string $href): string
    {
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '//')) {
            return 'https:'.$href;
        }

        return self::BPK_BASE_URL.'/'.ltrim($href, '/');
    }

    private function fail(Regulation $regulation, string $status, string $message, ?string $pdfUrl = null): array
    {
        $message = $this->cleanMessage($message);
        $regulation->update([
            'download_status' => $status,
            'download_error' => $message,
            'pdf_url' => $pdfUrl ?: $regulation->pdf_url,
        ]);

        return [
            'success' => false,
            'message' => $message,
            'pdf_url' => $pdfUrl,
            'file_path' => null,
        ];
    }

    private function cleanMessage(string $message): string
    {
        $message = @iconv('UTF-8', 'UTF-8//IGNORE', $message) ?: $message;
        $message = preg_replace('/[\x{D800}-\x{DFFF}]/u', '', $message) ?: $message;

        return Str::limit(trim($message), 2000, '');
    }

    private function filename(Regulation $regulation, string $pdfUrl): string
    {
        return Str::slug($regulation->title ?: 'regulasi-'.$regulation->id).'.pdf';
    }

    private function htmlHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,application/pdf,*/*;q=0.8',
        ];
    }

    private function pdfHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0',
            'Accept' => 'application/pdf,*/*',
        ];
    }
}
