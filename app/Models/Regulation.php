<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    protected $fillable = [
        'title', 'regulation_number', 'year', 'category', 'priority', 'description', 'usage_notes',
        'official_url', 'pdf_url', 'download_status', 'download_error', 'downloaded_at',
        'can_download_by_participant', 'file_path', 'original_filename', 'mime_type', 'file_size', 'extracted_text', 'extraction_status',
        'extraction_method', 'extraction_error', 'extracted_at', 'page_count', 'ocr_language',
        'ocr_confidence', 'summary', 'keywords', 'status', 'uploaded_by',
    ];

    protected $casts = [
        'extracted_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'can_download_by_participant' => 'boolean',
        'keywords' => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function generatedQuestions()
    {
        return $this->hasMany(GeneratedQuestion::class);
    }

    public function pages()
    {
        return $this->hasMany(RegulationPage::class)->orderBy('page_number');
    }

    public function isPdf(): bool
    {
        return str_contains((string) $this->mime_type, 'pdf') || strtolower(pathinfo((string) $this->file_path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
