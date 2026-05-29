<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nip',
        'tanggal_lahir',
        'status_asn',
        'pendidikan_terakhir',
        'jurusan_pendidikan',
        'jabatan_saat_ini',
        'jenis_jabatan_saat_ini',
        'kategori_jabatan_fungsional',
        'jenjang_jabatan_saat_ini',
        'golongan_ruang',
        'tmt_pangkat_terakhir',
        'tmt_jabatan_terakhir',
        'angka_kredit_terakhir',
        'unit_kerja',
        'instansi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pangkat_terakhir' => 'date',
        'tmt_jabatan_terakhir' => 'date',
        'angka_kredit_terakhir' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
