<?php

namespace Database\Seeders;

use App\Models\MasterJenjangAk;
use App\Models\MasterPredikatKinerja;
use Illuminate\Database\Seeder;

class AngkaKreditMasterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('angka_kredit.predikat') as $nama => $persentase) {
            MasterPredikatKinerja::updateOrCreate(
                ['nama_predikat' => $nama],
                ['persentase' => $persentase, 'is_active' => true]
            );
        }

        $jenjang = [
            ['Keahlian', 'Ahli Pertama', 'III/a', 'III/b', 12.5, 0, 50, 100],
            ['Keahlian', 'Ahli Muda', 'III/c', 'III/d', 25, 0, 100, 200],
            ['Keahlian', 'Ahli Madya', 'IV/a', 'IV/c', 37.5, 0, 150, 450],
            ['Keahlian', 'Ahli Utama', 'IV/d', 'IV/e', 50, 0, 200, null],
            ['Keterampilan', 'Pemula', 'II/a', 'II/a', 3.75, 0, 15, 15],
            ['Keterampilan', 'Terampil', 'II/b', 'II/d', 5, 0, 20, 60],
            ['Keterampilan', 'Mahir', 'III/a', 'III/b', 12.5, 0, 50, 100],
            ['Keterampilan', 'Penyelia', 'III/c', 'III/d', 25, 0, 100, null],
        ];

        foreach ($jenjang as [$kategori, $nama, $min, $max, $koefisien, $dasar, $pangkat, $jenjangAk]) {
            MasterJenjangAk::updateOrCreate(
                ['kategori' => $kategori, 'jenjang' => $nama],
                [
                    'golongan_min' => $min,
                    'golongan_max' => $max,
                    'koefisien_ak_tahunan' => $koefisien,
                    'ak_dasar' => $dasar,
                    'kebutuhan_ak_pangkat' => $pangkat,
                    'kebutuhan_ak_jenjang' => $jenjangAk,
                ]
            );
        }
    }
}
