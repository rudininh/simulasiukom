<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nama_lengkap')->nullable();
            $table->string('nip')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('status_asn')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('jurusan_pendidikan')->nullable();
            $table->string('jabatan_saat_ini')->nullable();
            $table->string('jenis_jabatan_saat_ini')->nullable();
            $table->string('kategori_jabatan_fungsional')->nullable();
            $table->string('jenjang_jabatan_saat_ini')->nullable();
            $table->string('golongan_ruang')->nullable();
            $table->date('tmt_pangkat_terakhir')->nullable();
            $table->date('tmt_jabatan_terakhir')->nullable();
            $table->decimal('angka_kredit_terakhir', 10, 4)->nullable();
            $table->string('unit_kerja')->nullable();
            $table->string('instansi')->nullable();
            $table->timestamps();
        });

        Schema::create('master_predikat_kinerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_predikat')->unique();
            $table->decimal('persentase', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('master_jenjang_ak', function (Blueprint $table) {
            $table->id();
            $table->string('kategori')->nullable();
            $table->string('jenjang');
            $table->string('golongan_min')->nullable();
            $table->string('golongan_max')->nullable();
            $table->decimal('koefisien_ak_tahunan', 10, 4)->nullable();
            $table->decimal('ak_dasar', 10, 4)->nullable();
            $table->decimal('kebutuhan_ak_pangkat', 10, 4)->nullable();
            $table->decimal('kebutuhan_ak_jenjang', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('simulasi_angka_kredits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_simulasi');
            $table->string('jabatan_asal')->nullable();
            $table->string('jabatan_tujuan')->nullable();
            $table->string('golongan_ruang')->nullable();
            $table->string('jenjang_tujuan')->nullable();
            $table->json('data_input')->nullable();
            $table->json('rincian_hasil')->nullable();
            $table->decimal('angka_kredit_lama', 10, 4)->nullable();
            $table->decimal('angka_kredit_konversi', 10, 4)->nullable();
            $table->decimal('angka_kredit_dasar', 10, 4)->nullable();
            $table->decimal('tambahan_ak_ijazah', 10, 4)->nullable();
            $table->decimal('total_ak', 10, 4)->nullable();
            $table->decimal('kebutuhan_ak_kenaikan_pangkat', 10, 4)->nullable();
            $table->decimal('kebutuhan_ak_kenaikan_jenjang', 10, 4)->nullable();
            $table->string('status_kp')->nullable();
            $table->string('status_kj')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulasi_angka_kredits');
        Schema::dropIfExists('master_jenjang_ak');
        Schema::dropIfExists('master_predikat_kinerja');
        Schema::dropIfExists('user_profiles');
    }
};
