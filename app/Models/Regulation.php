<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    protected $fillable = ['title', 'regulation_number', 'year', 'description', 'file_path', 'extracted_text', 'status', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function generatedQuestions()
    {
        return $this->hasMany(GeneratedQuestion::class);
    }
}
