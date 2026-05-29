<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJenjangAk extends Model
{
    protected $table = 'master_jenjang_ak';

    protected $fillable = [
        'kategori',
        'jenjang',
        'golongan_min',
        'golongan_max',
        'koefisien_ak_tahunan',
        'ak_dasar',
        'kebutuhan_ak_pangkat',
        'kebutuhan_ak_jenjang',
    ];

    protected $casts = [
        'koefisien_ak_tahunan' => 'decimal:4',
        'ak_dasar' => 'decimal:4',
        'kebutuhan_ak_pangkat' => 'decimal:4',
        'kebutuhan_ak_jenjang' => 'decimal:4',
    ];
}
