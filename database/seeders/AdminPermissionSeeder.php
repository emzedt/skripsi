<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JabatanPermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    public function run()
    {
        // Find the Admin position (id_jabatan = 1)
        $admin = Jabatan::find(1);

        if (!$admin) {
            $this->command->error('Jabatan Admin dengan ID 1 tidak ditemukan!');
            return;
        }

        // Get all permissions
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->command->error('Tidak ada permission yang tersedia!');
            return;
        }

        // Sync all permissions to admin role
        $admin->permissions()->sync($permissions->pluck('id'));

        $this->command->info('Berhasil menambahkan semua permission ke jabatan Admin (ID: 1)');
    }
}
