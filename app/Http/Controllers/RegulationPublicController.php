<?php

namespace App\Http\Controllers;

use App\Models\Regulation;

class RegulationPublicController extends Controller
{
    public function index()
    {
        return view('regulations.index', [
            'regulations' => Regulation::where('status', 'active')->latest()->get(),
        ]);
    }
}
