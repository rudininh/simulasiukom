<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AsnSimulationPreparationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator CAT',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'peserta@example.com'], [
            'name' => 'Rudini Nor Habibi',
            'username' => 'peserta',
            'password' => Hash::make('password'),
            'role' => 'peserta',
            'phone' => '081234567890',
            'institution' => 'Instansi Pemerintah',
            'position_name' => 'Analis SDM Aparatur',
            'work_unit' => 'Bidang Pengembangan Kompetensi',
            'employee_number' => '198901012020121001',
        ]);

        $simulation = app(AsnSimulationPreparationService::class);
        $simulation->seedRegulations($admin->id);
        $simulation->seedCoursesAndQuestions();
    }
}
