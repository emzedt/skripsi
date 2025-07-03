<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\HakCuti;
use App\Models\Jabatan;
use App\Models\StatusKaryawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = User::with('jabatan:id,nama')->select('users.*');
            return DataTables::of($user)->make(true);
        }

        return view('karyawan.index');
    }

    public function create()
    {
        $jabatans = Jabatan::select('id', 'nama')->get();
        $hakCutis = HakCuti::select('id', 'hak_cuti', 'hak_cuti_bersama')->get();
        $statusKaryawans = StatusKaryawan::select('id', 'status_karyawan')->get();

        return view('karyawan.create', compact('jabatans', 'hakCutis', 'statusKaryawans'));
    }

    public function store(UserRequest $request)
    {
        $hakCuti = HakCuti::select('hak_cuti', 'hak_cuti_bersama')->findOrFail($request->hak_cuti_id);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'no_rekening' => $request->no_rekening,
            'password' => Hash::make($request->password),
            'jabatan_id' => $request->jabatan_id,
            'hak_cuti_id' => $request->hak_cuti_id,
            'sisa_hak_cuti' => $hakCuti->hak_cuti,
            'sisa_hak_cuti_bersama' => $hakCuti->hak_cuti_bersama,
            'status_karyawan_id' => $request->status_karyawan_id
        ];

        User::create($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan Berhasil Ditambahkan!');
    }

    public function edit(User $user)
    {
        $jabatans = Jabatan::select('id', 'nama')->get();
        $statusKaryawans = StatusKaryawan::select('id', 'status_karyawan')->get();

        return view('karyawan.edit', compact('user', 'jabatans', 'statusKaryawans'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'no_rekening' => $request->no_rekening,
            'sisa_hak_cuti' => $request->sisa_hak_cuti,
            'sisa_hak_cuti_bersama' => $request->sisa_hak_cuti_bersama,
            'status_karyawan_id' => $request->status_karyawan_id,
            'jabatan_id' => $request->jabatan_id,
        ];

        $user->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        try {
            // Hapus karyawan
            $user->delete();

            // Return JSON sukses
            return response()->json([
                'success' => true,
                'message' => "Karyawan $user->nama berhasil dihapus."
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error jika ada data yang terkait (foreign key constraint)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => "Karyawan $user->nama tidak dapat dihapus karena masih memiliki data yang terkait."
                ], 400);
            }

            // Tangani error lain
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan saat menghapus karyawan $user->nama. Silakan coba lagi."
            ], 500);
        }
    }
}
