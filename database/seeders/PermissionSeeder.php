<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Employee Permissions
            ['nama' => 'Lihat Karyawan', 'group' => 'Karyawan'],
            ['nama' => 'Tambah Karyawan', 'group' => 'Karyawan'],
            ['nama' => 'Edit Karyawan', 'group' => 'Karyawan'],
            ['nama' => 'Hapus Karyawan', 'group' => 'Karyawan'],

            // Position (Jabatan) Permissions
            ['nama' => 'Lihat Jabatan', 'group' => 'Jabatan'],
            ['nama' => 'Tambah Jabatan', 'group' => 'Jabatan'],
            ['nama' => 'Edit Jabatan', 'group' => 'Jabatan'],
            ['nama' => 'Hapus Jabatan', 'group' => 'Jabatan'],
            ['nama' => 'Kelola Hirarki Jabatan', 'group' => 'Jabatan'],

            // Access Rights (Hak Akses) Permissions
            ['nama' => 'Lihat Hak Akses', 'group' => 'Hak Akses'],
            ['nama' => 'Edit Hak Akses', 'group' => 'Hak Akses'],

            // Employee Status (Status Karyawan) Permissions
            ['nama' => 'Lihat Status Karyawan', 'group' => 'Status Karyawan'],
            ['nama' => 'Tambah Status Karyawan', 'group' => 'Status Karyawan'],
            ['nama' => 'Edit Status Karyawan', 'group' => 'Status Karyawan'],
            ['nama' => 'Hapus Status Karyawan', 'group' => 'Status Karyawan'],

            // Location (Lokasi) Permissions
            ['nama' => 'Lihat Lokasi', 'group' => 'Lokasi'],
            ['nama' => 'Tambah Lokasi', 'group' => 'Lokasi'],
            ['nama' => 'Edit Lokasi', 'group' => 'Lokasi'],
            ['nama' => 'Hapus Lokasi', 'group' => 'Lokasi'],

            // Leave Rights (Hak Cuti) Permissions
            ['nama' => 'Lihat Hak Cuti', 'group' => 'Hak Cuti'],
            ['nama' => 'Kelola Hak Cuti', 'group' => 'Hak Cuti'],

            // Face Registration Permissions
            ['nama' => 'Kelola Registrasi Wajah', 'group' => 'Absensi'],

            // Attendance (Absensi) Permissions
            ['nama' => 'Lihat Absensi', 'group' => 'Absensi'],
            ['nama' => 'Tambah Absensi', 'group' => 'Absensi'],
            ['nama' => 'Edit Absensi', 'group' => 'Absensi'],
            ['nama' => 'Hapus Absensi', 'group' => 'Absensi'],

            ['nama' => 'Lihat Absensi Sales', 'group' => 'Absensi Sales'],
            ['nama' => 'Tambah Absensi Sales', 'group' => 'Absensi Sales'],
            ['nama' => 'Edit Absensi Sales', 'group' => 'Absensi Sales'],
            ['nama' => 'Lihat Detail Absensi Sales', 'group' => 'Absensi Sales'],
            ['nama' => 'Hapus Absensi Sales', 'group' => 'Absensi Sales'],

            // Leave (Cuti) Permissions
            ['nama' => 'Lihat Pengajuan Cuti', 'group' => 'Cuti'],
            ['nama' => 'Tambah Pengajuan Cuti', 'group' => 'Cuti'],
            ['nama' => 'Edit Pengajuan Cuti', 'group' => 'Cuti'],
            ['nama' => 'Hapus Pengajuan Cuti', 'group' => 'Cuti'],
            ['nama' => 'Lihat Persetujuan Cuti', 'group' => 'Cuti'],
            ['nama' => 'Proses Persetujuan Cuti', 'group' => 'Cuti'],

            // Sick (Sakit) Permissions
            ['nama' => 'Lihat Pengajuan Sakit', 'group' => 'Sakit'],
            ['nama' => 'Tambah Pengajuan Sakit', 'group' => 'Sakit'],
            ['nama' => 'Edit Pengajuan Sakit', 'group' => 'Sakit'],
            ['nama' => 'Hapus Pengajuan Sakit', 'group' => 'Sakit'],

            // Excuse (Izin) Permissions
            ['nama' => 'Lihat Pengajuan Izin', 'group' => 'Izin'],
            ['nama' => 'Tambah Pengajuan Izin', 'group' => 'Izin'],
            ['nama' => 'Edit Pengajuan Izin', 'group' => 'Izin'],
            ['nama' => 'Hapus Pengajuan Izin', 'group' => 'Izin'],

            // Salary (Gaji) Permissions
            ['nama' => 'Lihat Gaji', 'group' => 'Gaji'],
            ['nama' => 'Tambah Gaji', 'group' => 'Gaji'],
            ['nama' => 'Edit Gaji', 'group' => 'Gaji'],
            ['nama' => 'Hapus Gaji', 'group' => 'Gaji'],

            // Payroll (Penggajian) Permissions
            ['nama' => 'Lihat Penggajian', 'group' => 'Penggajian'],
            ['nama' => 'Tambah Penggajian', 'group' => 'Penggajian'],
            ['nama' => 'Edit Penggajian', 'group' => 'Penggajian'],
            ['nama' => 'Hapus Penggajian', 'group' => 'Penggajian'],

            // Overtime (Lembur) Permissions
            ['nama' => 'Lihat Permintaan Lembur', 'group' => 'Lembur'],
            ['nama' => 'Tambah Permintaan Lembur', 'group' => 'Lembur'],
            ['nama' => 'Edit Permintaan Lembur', 'group' => 'Lembur'],
            ['nama' => 'Hapus Permintaan Lembur', 'group' => 'Lembur'],

            // People Development Permissions
            ['nama' => 'Lihat People Development', 'group' => 'Pengembangan'],
            ['nama' => 'Tambah People Development', 'group' => 'Pengembangan'],
            ['nama' => 'Edit People Development', 'group' => 'Pengembangan'],
            ['nama' => 'Hapus People Development', 'group' => 'Pengembangan'],

            // Kalender Permissions
            ['nama' => 'Lihat Kalender', 'group' => 'Kalender'],
            ['nama' => 'Kelola Hari Libur', 'group' => 'Kalender'],

            // Kelola Trash
            ['nama' => 'Kelola Sampah', 'group' => 'Sampah'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
