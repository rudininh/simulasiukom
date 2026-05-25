<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulationPage extends Model
{
    protected $fillable = [
        'regulation_id', 'page_number', 'text', 'ocr_text', 'extraction_method',
        'confidence', 'image_path', 'status', 'error_message',
    ];

    public function regulation()
    {
        return $this->belongsTo(Regulation::class);
    }
}
