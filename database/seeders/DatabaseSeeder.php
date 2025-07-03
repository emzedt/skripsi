<?php

namespace Database\Seeders;

use App\Models\HakCuti;
use App\Models\Jabatan;
use App\Models\StatusKaryawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Jabatan::insert([
            ['nama' => 'Admin'],
            ['nama' => 'Operational Director'],
            ['nama' => 'Sales and Marketing Director'],
        ]);

        HakCuti::create([
            'hak_cuti' => 12,
            'hak_cuti_bersama' => 5
        ]);

        StatusKaryawan::insert([
            ['status_karyawan' => 'Karyawan Tetap'],
            ['status_karyawan' => 'Karyawan Harian'],
        ]);

        User::create([
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '08129128392',
            'no_rekening' => '1234567898',
            'sisa_hak_cuti' => 12,
            'sisa_hak_cuti_bersama' => 5,
            'jabatan_id' => 1,
            'hak_cuti_id' =>  1,
            'status_karyawan_id' => 1
        ]);

        $this->call(PermissionSeeder::class);
        $this->call(AdminPermissionSeeder::class);
    }
}
