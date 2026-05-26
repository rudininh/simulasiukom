<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regulation;
use App\Services\RegulationOcrService;
use App\Services\RegulationPdfDownloaderService;
use App\Services\RegulationSummaryService;
use App\Services\RegulationTextExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegulationController extends Controller
{
    public function index()
    {
        return view('admin.regulations.index', [
            'regulations' => Regulation::with('uploader', 'generatedQuestions')->latest()->paginate(15),
        ]);
    }

    public function store(Request $request, RegulationTextExtractorService $extractor)
    {
        $data = $this->validated($request);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('regulations', 'public');
            $data['original_filename'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }
        $data['uploaded_by'] = auth()->id();
        $data['ocr_language'] = config('ocr.language');
        $regulation = Regulation::create($data);

        try {
            $extractor->extract($regulation);
        } catch (\Throwable $e) {
            return redirect()->route('admin.regulations.show', $regulation)->withErrors(['file' => $e->getMessage()]);
        }

        return redirect()->route('admin.regulations.show', $regulation)->with('success', 'Regulasi berhasil diupload.');
    }

    public function show(Regulation $regulation)
    {
        $regulation->load('pages', 'generatedQuestions');
        return view('admin.regulations.show', compact('regulation'));
    }

    public function update(Request $request, Regulation $regulation)
    {
        $data = $this->validated($request);
        if (!$request->hasFile('file') && $regulation->file_path && empty($data['pdf_url'])) {
            unset($data['download_status']);
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('regulations', 'public');
            $data['original_filename'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
            $data['extraction_status'] = 'pending';
        }
        $regulation->update($data);

        return back()->with('success', 'Regulasi diperbarui.');
    }

    public function destroy(Regulation $regulation)
    {
        $regulation->delete();
        return redirect()->route('admin.regulations.index')->with('success', 'Regulasi dihapus.');
    }

    public function preview(Regulation $regulation)
    {
        abort_unless($regulation->file_path && Storage::disk('public')->exists($regulation->file_path), 404);
        return view('admin.regulations.preview', compact('regulation'));
    }

    public function download(Regulation $regulation)
    {
        abort_unless($regulation->file_path && Storage::disk('public')->exists($regulation->file_path), 404);
        return Storage::disk('public')->download($regulation->file_path, $regulation->original_filename ?: basename($regulation->file_path));
    }

    public function downloadPdf(Regulation $regulation, RegulationPdfDownloaderService $downloader)
    {
        $ok = $downloader->download($regulation);
        return back()->with($ok ? 'success' : 'error', $ok ? 'PDF regulasi berhasil diunduh.' : 'Download PDF gagal. Silakan cek pesan error atau upload manual.');
    }

    public function downloadAllPdfs(RegulationPdfDownloaderService $downloader)
    {
        $success = 0;
        $failed = 0;

        Regulation::whereNotNull('pdf_url')->where('pdf_url', '<>', '')->each(function (Regulation $regulation) use ($downloader, &$success, &$failed) {
            $downloader->download($regulation) ? $success++ : $failed++;
        });

        Regulation::where(function ($query) {
            $query->whereNull('pdf_url')->orWhere('pdf_url', '');
        })->update(['download_status' => 'manual_required']);

        return back()->with('success', "Download selesai. Berhasil: {$success}, gagal: {$failed}.");
    }

    public function extractText(Regulation $regulation, RegulationTextExtractorService $extractor)
    {
        try {
            $extractor->extract($regulation);
            return back()->with('success', $regulation->fresh()->extraction_status === 'need_ocr'
                ? 'PDF tidak memiliki teks yang dapat dibaca. Silakan jalankan OCR.'
                : 'Teks regulasi berhasil diekstrak.');
        } catch (\Throwable $e) {
            return back()->withErrors(['extract' => $e->getMessage()]);
        }
    }

    public function ocr(Regulation $regulation, RegulationOcrService $ocr)
    {
        try {
            $ocr->process($regulation);
            return back()->with('success', 'OCR selesai. Teks siap digunakan untuk generate soal.');
        } catch (\Throwable $e) {
            return back()->withErrors(['ocr' => $e->getMessage()]);
        }
    }

    public function text(Regulation $regulation)
    {
        $regulation->load('pages');
        return view('admin.regulations.text', compact('regulation'));
    }

    public function downloadText(Regulation $regulation)
    {
        $filename = 'regulasi-'.$regulation->id.'.txt';
        return response($regulation->extracted_text ?: '')
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function summarize(Regulation $regulation, RegulationSummaryService $summary)
    {
        $summary->summarize($regulation);
        return back()->with('success', 'Ringkasan regulasi berhasil dibuat.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'regulation_number' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'usage_notes' => ['nullable', 'string'],
            'official_url' => ['nullable', 'url', 'max:255'],
            'pdf_url' => ['nullable', 'url', 'max:255'],
            'can_download_by_participant' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'mimes:pdf,docx,txt', 'max:20480'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'file.mimes' => 'Format file tidak didukung.',
        ]);

        $data['can_download_by_participant'] = $request->boolean('can_download_by_participant');
        if ($request->hasFile('file')) {
            $data['download_status'] = 'downloaded';
            $data['download_error'] = null;
            $data['downloaded_at'] = now();
        } elseif (empty($data['pdf_url'])) {
            $data['download_status'] = 'manual_required';
        }

        return $data;
    }
}
