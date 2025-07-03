<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GajiBulanan;
use App\Models\GajiHarian;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek jika ini adalah permintaan AJAX dari DataTables
        if ($request->ajax()) {

            // Closure Aksi yang bisa dipakai ulang untuk kedua tabel
            $aksi = function ($karyawan) use ($request) {
                $user = Auth::user();
                $editUrl = route('gaji.edit', $karyawan->id);
                // Kita akan memberitahu fungsi delete tabel mana yang harus di-reload
                $tableId = $request->type == 'tetap' ? '#gaji-tetap-table' : '#gaji-harian-table';
                $deleteFunc = "confirmDelete({$karyawan->id}, '{$karyawan->nama}', '$tableId')";

                $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';


                $actions = '<div class="flex space-x-2 justify-center">';
                if ($user->hasPermission('Edit Gaji')) {
                    // Ganti '...' dengan SVG ikon Anda
                    $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">' . $editIcon . '</a>';
                }
                if ($user->hasPermission('Hapus Gaji')) {
                    $actions .= '<button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>';
                }
                $actions .= '</div>';
                return $actions;
            };

            // 2. Logika untuk Karyawan Tetap
            if ($request->type == 'tetap') {
                $query = User::with('gajiBulanan')
                    ->whereHas('statusKaryawan', fn($q) => $q->where('status_karyawan', 'Karyawan Tetap'))
                    ->whereHas('gajiBulanan');

                return DataTables::of($query)
                    ->addColumn('gaji_bulanan', fn($user) => 'Rp ' . number_format($user->gajiBulanan->gaji_bulanan ?? 0, 0, ',', '.'))
                    ->addColumn('aksi', $aksi)
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            // 3. Logika untuk Karyawan Harian
            if ($request->type == 'harian') {
                $query = User::with(['gajiHarian', 'lembur'])
                    ->whereHas('statusKaryawan', fn($q) => $q->where('status_karyawan', 'Karyawan Harian'))
                    ->whereHas('gajiHarian');

                return DataTables::of($query)
                    ->addColumn('gaji_harian', fn($user) => 'Rp ' . number_format($user->gajiHarian->gaji_harian ?? 0, 0, ',', '.'))
                    ->addColumn('upah_makan_harian', fn($user) => 'Rp ' . number_format($user->gajiHarian->upah_makan_harian ?? 0, 0, ',', '.'))
                    ->addColumn('lembur_per_jam', fn($user) => 'Rp ' . number_format($user->lembur->upah_lembur_per_jam ?? 0, 0, ',', '.'))
                    ->addColumn('lembur_over_5_jam', fn($user) => 'Rp ' . number_format($user->lembur->upah_lembur_over_5_jam ?? 0, 0, ',', '.'))
                    ->addColumn('aksi', $aksi)
                    ->rawColumns(['aksi'])
                    ->make(true);
            }
        }

        // 4. Untuk permintaan biasa (bukan AJAX), hanya tampilkan view
        return view('gaji.index');
    }

    public function create()
    {
        $users = User::whereDoesntHave('gajiBulanan')
            ->whereDoesntHave('gajiHarian')
            ->with('statusKaryawan')
            ->get();

        return view('gaji.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        // Bersihkan format mata uang
        $gajiBulanan = str_replace(['Rp', '.', ' '], '', $request->gaji_bulanan);
        $gajiHarian = str_replace(['Rp', '.', ' '], '', $request->gaji_harian);
        $upahMakan = str_replace(['Rp', '.', ' '], '', $request->upah_makan_harian);
        $lemburPerJam = str_replace(['Rp', '.', ' '], '', $request->upah_lembur_per_jam);
        $lemburOver5Jam = str_replace(['Rp', '.', ' '], '', $request->upah_lembur_over_5_jam);

        if ($user->isKaryawanHarian()) {
            $gaji = GajiHarian::create(
                [
                    'user_id' => $user->id,
                    'gaji_harian' => (int)$gajiHarian,
                    'upah_makan_harian' => (int)$upahMakan
                ]
            );
            Lembur::create(
                [
                    'user_id' => $user->id,
                    'upah_lembur_per_jam' => (int)$lemburPerJam,
                    'upah_lembur_over_5_jam' => (int)$lemburOver5Jam
                ]
            );
        } else {
            $gaji = GajiBulanan::create(
                [
                    'user_id' => $user->id,
                    'gaji_bulanan' => (int)$gajiBulanan
                ]
            );
        }

        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil disimpan');
    }

    public function edit($id)
    {
        $user = User::with(['gajiBulanan', 'gajiHarian', 'lembur'])->findOrFail($id);
        $users = User::all();

        return view('gaji.edit', compact('user', 'users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Bersihkan format mata uang
        $gajiBulanan = str_replace(['Rp', '.', ' '], '', $request->gaji_bulanan);
        $gajiHarian = str_replace(['Rp', '.', ' '], '', $request->gaji_harian);
        $upahMakan = str_replace(['Rp', '.', ' '], '', $request->upah_makan_harian);
        $lemburPerJam = str_replace(['Rp', '.', ' '], '', $request->upah_lembur_per_jam);
        $lemburOver5Jam = str_replace(['Rp', '.', ' '], '', $request->upah_lembur_over_5_jam);

        if ($user->isKaryawanHarian()) {
            $gaji = GajiHarian::select('gaji_harian', 'upah_makan_harian', 'user_id')->where('user_id', $user->id)->first();
            $gaji->update(
                [
                    'user_id' => $user->id,
                    'gaji_harian' => (int)$gajiHarian,
                    'upah_makan_harian' => (int)$upahMakan,
                ]
            );
            $lembur = Lembur::select('upah_lembur_per_jam', 'upah_lembur_over_5_jam', 'user_id')->where('user_id', $user->id)->first();
            $lembur->update(
                [
                    'user_id' => $user->id,
                    'upah_lembur_per_jam' => (int)$lemburPerJam,
                    'upah_lembur_over_5_jam' => (int)$lemburOver5Jam
                ]
            );
        } else {
            $gaji = GajiBulanan::select('gaji_bulanan', 'user_id')->where('user_id', $user->id)->first();
            $cleanedGaji = (float) str_replace(',', '.', str_replace(['Rp', '.', ' '], '', $gajiBulanan));
            GajiBulanan::updateOrCreate(
                ['user_id' => $user->id],
                ['gaji_bulanan' => $cleanedGaji]
            );
        }

        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->isKaryawanTetap()) {
            $user->gajiBulanan()->delete();
        } else {
            $user->gajiHarian()->delete();
        }

        $user->lembur()->delete();

        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil dihapus');
    }
}
