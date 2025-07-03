<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class HakAksesController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek jika ini adalah permintaan dari DataTables
        if ($request->ajax()) {

            // 2. Buat query dasar dengan eager loading relationship 'permissions'
            $query = Jabatan::with('permissions');

            return DataTables::of($query)
                ->addColumn('hak_akses_list', function ($jabatan) {
                    // 3. Buat kolom kustom untuk menampilkan daftar hak akses
                    if ($jabatan->permissions->isEmpty()) {
                        return '<span class="text-sm text-gray-500 italic">Belum ada hak akses</span>';
                    }

                    // Ubah koleksi permissions menjadi badge HTML
                    return $jabatan->permissions->map(function ($permission) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">' .
                            $permission->name .
                            '</span>';
                    })->implode(''); // Gabungkan semua badge menjadi satu string HTML
                })
                ->addColumn('aksi', function ($jabatan) {
                    // 4. Buat kolom kustom untuk tombol "Kelola"
                    $url = route('hak-akses.edit', $jabatan->id);
                    return '<a href="' . $url . '" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md">
                                Kelola
                            </a>';
                })
                ->rawColumns(['hak_akses_list', 'aksi']) // 5. Beritahu DataTables ada kolom dengan HTML
                ->make(true);
        }

        // 6. Untuk permintaan biasa (load halaman awal), kirim view-nya.
        // Variabel $permissions bisa tetap dikirim jika dibutuhkan di bagian lain halaman.
        $permissions = Permission::all();
        return view('hak_akses.index', compact('permissions'));
    }

    public function edit(Jabatan $jabatan)
    {
        $permissions = Permission::all()->groupBy('group');

        $parentJabatans = Jabatan::whereNotIn('jabatans.id', [$jabatan->id])
            ->whereDoesntHave('childJabatans', function ($q) use ($jabatan) {
                $q->where('jabatan_hierarchys.child_jabatan_id', $jabatan->id);
            })
            ->get();



        return view('hak_akses.edit', compact('jabatan', 'permissions', 'parentJabatans'));
    }

    // Update role permissions
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'parent_jabatan_id' => 'nullable',
        ]);

        // Update permissions
        $jabatan->permissions()->sync($request->permissions ?? []);

        // Clear all existing relationships first
        DB::table('jabatan_hierarchys')
            ->where('child_jabatan_id', $jabatan->id)
            ->delete();

        // Handle new parent selection
        if ($request->parent_jabatan_id && $request->parent_jabatan_id !== "Tidak Ada") {
            DB::table('jabatan_hierarchys')->insert([
                'parent_jabatan_id' => $request->parent_jabatan_id,
                'child_jabatan_id' => $jabatan->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('hak-akses.index')
            ->with('success', 'Hak akses berhasil diperbarui');
    }
}
