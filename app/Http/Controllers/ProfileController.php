<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'profile' => auth()->user()->profile,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
            'position_name' => ['nullable', 'string', 'max:255'],
            'work_unit' => ['nullable', 'string', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'status_asn' => ['nullable', Rule::in(['PNS', 'PPPK'])],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:255'],
            'jurusan_pendidikan' => ['nullable', 'string', 'max:255'],
            'jabatan_saat_ini' => ['nullable', 'string', 'max:255'],
            'jenis_jabatan_saat_ini' => ['nullable', Rule::in(['Pelaksana', 'Jabatan Administrasi - Pengawas', 'Jabatan Administrasi - Administrator', 'JPT', 'Jabatan Fungsional'])],
            'kategori_jabatan_fungsional' => ['nullable', Rule::in(['Keahlian', 'Keterampilan', 'Tidak Ada'])],
            'jenjang_jabatan_saat_ini' => ['nullable', Rule::in(['Pemula', 'Terampil', 'Mahir', 'Penyelia', 'Ahli Pertama', 'Ahli Muda', 'Ahli Madya', 'Ahli Utama', 'Tidak Ada'])],
            'golongan_ruang' => ['nullable', Rule::in(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'])],
            'tmt_pangkat_terakhir' => ['nullable', 'date'],
            'tmt_jabatan_terakhir' => ['nullable', 'date'],
            'angka_kredit_terakhir' => ['nullable', 'numeric', 'min:0'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
        ]);

        $profileData = collect($data)->only([
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
        ])->all();

        $userData = collect($data)->except(array_keys($profileData))->all();
        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
