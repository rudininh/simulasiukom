<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulasiAngkaKredit extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_simulasi',
        'jabatan_asal',
        'jabatan_tujuan',
        'golongan_ruang',
        'jenjang_tujuan',
        'data_input',
        'rincian_hasil',
        'angka_kredit_lama',
        'angka_kredit_konversi',
        'angka_kredit_dasar',
        'tambahan_ak_ijazah',
        'total_ak',
        'kebutuhan_ak_kenaikan_pangkat',
        'kebutuhan_ak_kenaikan_jenjang',
        'status_kp',
        'status_kj',
        'catatan',
    ];

    protected $casts = [
        'data_input' => 'array',
        'rincian_hasil' => 'array',
        'angka_kredit_lama' => 'decimal:4',
        'angka_kredit_konversi' => 'decimal:4',
        'angka_kredit_dasar' => 'decimal:4',
        'tambahan_ak_ijazah' => 'decimal:4',
        'total_ak' => 'decimal:4',
        'kebutuhan_ak_kenaikan_pangkat' => 'decimal:4',
        'kebutuhan_ak_kenaikan_jenjang' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
