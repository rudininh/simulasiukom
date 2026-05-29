<?php

namespace App\Http\Controllers;

use App\Models\MasterJenjangAk;
use App\Models\MasterPredikatKinerja;
use App\Models\SimulasiAngkaKredit;
use App\Services\AngkaKreditCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SimulasiAngkaKreditController extends Controller
{
    private const JENIS_SIMULASI = [
        'Perpindahan dari Jabatan Lain ke JF',
        'Perpindahan Antar Jabatan Fungsional',
        'Penyesuaian / Penyetaraan',
        'Promosi ke dalam JF',
        'Kenaikan Jenjang Jabatan Fungsional',
        'Kenaikan Pangkat Jabatan Fungsional',
        'Pengangkatan Kembali',
        'Tambahan Angka Kredit karena Ijazah',
    ];

    public function index(Request $request)
    {
        return view('simulasi_angka_kredit.index', [
            'data' => $this->defaultData($request),
            'result' => null,
            ...$this->viewData($request),
        ]);
    }

    public function calculate(Request $request, AngkaKreditCalculatorService $calculator)
    {
        $data = $this->validated($request);

        if ($request->input('action') === 'save_profile') {
            $this->saveSimulationDataToProfile($request, $data);

            return back()->with('success', 'Data simulasi berhasil disimpan ke profil.');
        }

        $result = $calculator->calculate($data);

        return view('simulasi_angka_kredit.index', [
            'data' => $data,
            'result' => $result,
            ...$this->viewData($request),
        ]);
    }

    public function store(Request $request, AngkaKreditCalculatorService $calculator)
    {
        $data = $this->validated($request);
        $result = $calculator->calculate($data);

        $simulation = SimulasiAngkaKredit::create([
            'user_id' => $request->user()->id,
            'jenis_simulasi' => $data['jenis_simulasi'],
            'jabatan_asal' => $data['jabatan_asal'] ?? null,
            'jabatan_tujuan' => $data['jabatan_tujuan'] ?? null,
            'golongan_ruang' => $data['golongan_ruang'] ?? null,
            'jenjang_tujuan' => $data['jenjang_jf_tujuan'] ?? null,
            'data_input' => $data,
            'rincian_hasil' => $result,
            'angka_kredit_lama' => $result['angka_kredit_lama'],
            'angka_kredit_konversi' => $result['angka_kredit_konversi'],
            'angka_kredit_dasar' => $result['angka_kredit_dasar'],
            'tambahan_ak_ijazah' => $result['tambahan_ak_ijazah'],
            'total_ak' => $result['total_ak'],
            'kebutuhan_ak_kenaikan_pangkat' => $result['kebutuhan_ak_kenaikan_pangkat'],
            'kebutuhan_ak_kenaikan_jenjang' => $result['kebutuhan_ak_kenaikan_jenjang'],
            'status_kp' => $result['status_kp'],
            'status_kj' => $result['status_kj'],
            'catatan' => trim(($data['catatan'] ?? '').' '.$result['catatan']),
        ]);

        return redirect()->route('angka-kredit.show', $simulation)->with('success', 'Hasil simulasi berhasil disimpan.');
    }

    public function history(Request $request)
    {
        $simulations = SimulasiAngkaKredit::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('simulasi_angka_kredit.history', compact('simulations'));
    }

    public function show(Request $request, SimulasiAngkaKredit $simulasiAngkaKredit)
    {
        abort_unless($simulasiAngkaKredit->user_id === $request->user()->id, 403);

        return view('simulasi_angka_kredit.show', [
            'simulation' => $simulasiAngkaKredit,
            'result' => $simulasiAngkaKredit->rincian_hasil,
        ]);
    }

    public function destroy(Request $request, SimulasiAngkaKredit $simulasiAngkaKredit)
    {
        abort_unless($simulasiAngkaKredit->user_id === $request->user()->id, 403);
        $simulasiAngkaKredit->delete();

        return redirect()->route('angka-kredit.history')->with('success', 'Riwayat simulasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $predikat = MasterPredikatKinerja::where('is_active', true)->pluck('nama_predikat')->all()
            ?: array_keys(config('angka_kredit.predikat'));

        $data = $request->validate([
            'jenis_simulasi' => ['required', Rule::in(self::JENIS_SIMULASI)],
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:100'],
            'jabatan_asal' => ['nullable', 'string', 'max:255'],
            'jenis_jabatan_asal' => ['nullable', 'string', 'max:255'],
            'jabatan_tujuan' => ['nullable', 'string', 'max:255'],
            'kategori_jf_tujuan' => ['nullable', 'string', 'max:100'],
            'jenjang_jf_tujuan' => ['nullable', 'string', 'max:100'],
            'golongan_ruang' => ['required', 'string', 'max:20'],
            'tmt_pangkat_terakhir' => ['nullable', 'date'],
            'tmt_jabatan_terakhir' => ['nullable', 'date'],
            'tanggal_simulasi' => ['required', 'date'],
            'masa_pangkat_tahun' => ['nullable', 'integer', 'min:0', 'max:60'],
            'masa_pangkat_bulan' => ['nullable', 'integer', 'min:0', 'max:11'],
            'predikat_kinerja' => ['required', Rule::in($predikat)],
            'angka_kredit_lama' => ['nullable', 'numeric', 'min:0'],
            'angka_kredit_dasar' => ['nullable', 'numeric', 'min:0'],
            'angka_kredit_penyesuaian' => ['nullable', 'numeric', 'min:0'],
            'koefisien_ak_tahunan' => ['nullable', 'numeric', 'min:0'],
            'kebutuhan_ak_kenaikan_pangkat' => ['nullable', 'numeric', 'min:0'],
            'kebutuhan_ak_kenaikan_jenjang' => ['nullable', 'numeric', 'min:0'],
            'pendidikan_baru' => ['nullable', 'string', 'max:255'],
            'kebutuhan_ak_pangkat_terkait' => ['nullable', 'numeric', 'min:0'],
            'pangkat_puncak' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string'],
            'riwayat_predikat' => ['nullable', 'array'],
            'riwayat_predikat.*.tahun' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'riwayat_predikat.*.jumlah_bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'riwayat_predikat.*.predikat_kinerja' => ['nullable', Rule::in($predikat)],
            'riwayat_predikat.*.koefisien_ak_tahunan' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['pangkat_puncak'] = $request->boolean('pangkat_puncak');
        $data['riwayat_predikat'] = array_values(array_filter($data['riwayat_predikat'] ?? [], function ($row) {
            return is_array($row) && !empty($row['jumlah_bulan']) && !empty($row['predikat_kinerja']);
        }));

        return $data;
    }

    private function defaultData(Request $request): array
    {
        $profile = $request->user()->profile;
        $tanggalSimulasi = old('tanggal_simulasi', now()->toDateString());
        $tmtPangkat = old('tmt_pangkat_terakhir', optional($profile?->tmt_pangkat_terakhir)->format('Y-m-d'));
        $masaPangkat = $this->masaPangkat($tmtPangkat, $tanggalSimulasi);
        $kategoriJf = old('kategori_jf_tujuan', $profile->kategori_jabatan_fungsional ?? null);
        $jenjangJf = old('jenjang_jf_tujuan', $profile->jenjang_jabatan_saat_ini ?? null);
        $master = $kategoriJf && $jenjangJf
            ? MasterJenjangAk::where('kategori', $kategoriJf)->where('jenjang', $jenjangJf)->first()
            : null;

        return [
            'nama_lengkap' => old('nama_lengkap', $profile->nama_lengkap ?? $request->user()->name),
            'nip' => old('nip', $profile->nip ?? $request->user()->employee_number),
            'jabatan_asal' => old('jabatan_asal', $profile->jabatan_saat_ini ?? $request->user()->position_name),
            'jenis_jabatan_asal' => old('jenis_jabatan_asal', $profile->jenis_jabatan_saat_ini ?? null),
            'jabatan_tujuan' => old('jabatan_tujuan'),
            'kategori_jf_tujuan' => $kategoriJf,
            'jenjang_jf_tujuan' => $jenjangJf,
            'golongan_ruang' => old('golongan_ruang', $profile->golongan_ruang ?? null),
            'tmt_pangkat_terakhir' => $tmtPangkat,
            'tmt_jabatan_terakhir' => old('tmt_jabatan_terakhir', optional($profile?->tmt_jabatan_terakhir)->format('Y-m-d')),
            'tanggal_simulasi' => $tanggalSimulasi,
            'masa_pangkat_tahun' => old('masa_pangkat_tahun', $masaPangkat['tahun']),
            'masa_pangkat_bulan' => old('masa_pangkat_bulan', $masaPangkat['bulan']),
            'predikat_kinerja' => old('predikat_kinerja', 'Baik'),
            'angka_kredit_lama' => old('angka_kredit_lama', $profile->angka_kredit_terakhir ?? null),
            'angka_kredit_dasar' => old('angka_kredit_dasar', $master?->ak_dasar ?? 0),
            'koefisien_ak_tahunan' => old('koefisien_ak_tahunan', $master?->koefisien_ak_tahunan),
            'kebutuhan_ak_kenaikan_pangkat' => old('kebutuhan_ak_kenaikan_pangkat', $master?->kebutuhan_ak_pangkat),
            'kebutuhan_ak_kenaikan_jenjang' => old('kebutuhan_ak_kenaikan_jenjang', $master?->kebutuhan_ak_jenjang),
            'kebutuhan_ak_pangkat_terkait' => old('kebutuhan_ak_pangkat_terkait', $master?->kebutuhan_ak_pangkat),
        ];
    }

    private function viewData(Request $request): array
    {
        $profile = $request->user()->profile;

        return [
            'jenisSimulasi' => self::JENIS_SIMULASI,
            'predikats' => MasterPredikatKinerja::where('is_active', true)->orderByDesc('persentase')->pluck('nama_predikat')->all()
                ?: array_keys(config('angka_kredit.predikat')),
            'masters' => MasterJenjangAk::orderBy('kategori')->orderBy('id')->get(),
            'masterOptions' => MasterJenjangAk::orderBy('kategori')->orderBy('id')->get()->map(fn ($item) => [
                'kategori' => $item->kategori,
                'jenjang' => $item->jenjang,
                'koefisien' => (float) $item->koefisien_ak_tahunan,
                'ak_dasar' => (float) $item->ak_dasar,
                'pangkat' => (float) $item->kebutuhan_ak_pangkat,
                'jenjang_ak' => $item->kebutuhan_ak_jenjang === null ? null : (float) $item->kebutuhan_ak_jenjang,
            ])->values(),
            'profile' => $profile,
            'profileIncomplete' => !$profile || empty($profile->nama_lengkap) || empty($profile->nip) || empty($profile->golongan_ruang) || empty($profile->jabatan_saat_ini),
        ];
    }

    private function saveSimulationDataToProfile(Request $request, array $data): void
    {
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'nama_lengkap' => $data['nama_lengkap'] ?? $request->user()->name,
                'nip' => $data['nip'] ?? $request->user()->employee_number,
                'jabatan_saat_ini' => $data['jabatan_asal'] ?? null,
                'jenis_jabatan_saat_ini' => $data['jenis_jabatan_asal'] ?? null,
                'kategori_jabatan_fungsional' => $data['kategori_jf_tujuan'] ?? null,
                'jenjang_jabatan_saat_ini' => $data['jenjang_jf_tujuan'] ?? null,
                'golongan_ruang' => $data['golongan_ruang'] ?? null,
                'tmt_pangkat_terakhir' => $data['tmt_pangkat_terakhir'] ?? null,
                'tmt_jabatan_terakhir' => $data['tmt_jabatan_terakhir'] ?? null,
                'angka_kredit_terakhir' => $data['angka_kredit_lama'] ?? null,
            ]
        );
    }

    private function masaPangkat(?string $tmtPangkat, ?string $tanggalSimulasi): array
    {
        if (!$tmtPangkat || !$tanggalSimulasi) {
            return ['tahun' => 0, 'bulan' => 0];
        }

        $start = Carbon::parse($tmtPangkat)->startOfDay();
        $end = Carbon::parse($tanggalSimulasi)->startOfDay();
        if ($end->lessThan($start)) {
            return ['tahun' => 0, 'bulan' => 0];
        }

        $months = (int) floor($start->diffInMonths($end));

        return [
            'tahun' => intdiv($months, 12),
            'bulan' => $months % 12,
        ];
    }
}
