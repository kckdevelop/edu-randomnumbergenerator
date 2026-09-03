<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GameSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin / Guru
        User::updateOrCreate(
            ['email' => 'guru@sekolah.id'],
            [
                'name' => 'Guru (Bandar Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'balance' => 999999999.00,
            ]
        );

        // 2. Akun Player / Siswa Sample (15 Siswa)
        $students = [
            [
                'name' => 'Budi Pratama',
                'email' => 'siswa1@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siswa2@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'win',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'siswa3@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'random_low_win',
            ],
            [
                'name' => 'Dewi Rahayu',
                'email' => 'siswa4@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Rizky Firmansyah',
                'email' => 'siswa5@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Nurul Hidayah',
                'email' => 'siswa6@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'random_low_win',
            ],
            [
                'name' => 'Fajar Santoso',
                'email' => 'siswa7@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Maya Indrawati',
                'email' => 'siswa8@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'win',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'siswa9@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Putri Anggraini',
                'email' => 'siswa10@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'random_low_win',
            ],
            [
                'name' => 'Dimas Prasetyo',
                'email' => 'siswa11@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Lestari Wulandari',
                'email' => 'siswa12@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Bagas Kurniawan',
                'email' => 'siswa13@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'random_low_win',
            ],
            [
                'name' => 'Ayu Permatasari',
                'email' => 'siswa14@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
            [
                'name' => 'Rafi Alfarizi',
                'email' => 'siswa15@sekolah.id',
                'balance' => 100000.00,
                'default_setting' => 'lose',
            ],
        ];

        foreach ($students as $studentData) {
            $user = User::updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'player',
                    'balance' => $studentData['balance'],
                ]
            );

            GameSetting::updateOrCreate(
                ['user_id' => $user->id],
                ['next_spin_result' => $studentData['default_setting']]
            );
        }
    }
}
