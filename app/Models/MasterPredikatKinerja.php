<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPredikatKinerja extends Model
{
    protected $table = 'master_predikat_kinerja';

    protected $fillable = [
        'nama_predikat',
        'persentase',
        'is_active',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
