<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use Illuminate\Support\Facades\Storage;

class RegulationPublicController extends Controller
{
    public function index()
    {
        return view('regulations.index', [
            'regulations' => Regulation::where('status', 'active')->latest()->get(),
        ]);
    }

    public function show(Regulation $regulation)
    {
        abort_unless($regulation->status === 'active', 404);
        $regulation->load('pages');

        return view('regulations.show', compact('regulation'));
    }

    public function preview(Regulation $regulation)
    {
        abort_unless($regulation->status === 'active', 404);
        abort_unless($regulation->file_path && Storage::disk('public')->exists($regulation->file_path), 404);

        return view('regulations.preview', compact('regulation'));
    }

    public function download(Regulation $regulation)
    {
        abort_unless($regulation->status === 'active' && $regulation->can_download_by_participant, 403);
        abort_unless($regulation->file_path && Storage::disk('public')->exists($regulation->file_path), 404);

        return Storage::disk('public')->download($regulation->file_path, $regulation->original_filename ?: basename($regulation->file_path));
    }
}
