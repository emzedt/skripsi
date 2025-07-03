<?php

namespace App\Http\Controllers;

use App\Http\Requests\CutiRequest;
use App\Mail\PermohonanDiajukanEmail;
use App\Mail\PermohonanDiketahuiEmail;
use App\Mail\PermohonanDiterimaEmail;
use App\Models\Cuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        // Bagian ini akan menangani AJAX request dari DataTables
        if ($request->ajax()) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                $query = Cuti::with('user:id,nama,sisa_hak_cuti');
            } else {
                $query = Cuti::with('user:id,nama,sisa_hak_cuti')->where(function ($q) use ($user) {
                    // Tampilkan data miliknya sendiri
                    $q->where('user_id', $user->id);

                    // DAN data milik bawahannya (jika ada)
                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $q->orWhereIn(
                            'user_id',
                            $user->subordinates()->select('users.id')
                        );
                    }
                });;
            }

            // Proses query menggunakan DataTables
            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom DT_RowIndex
                ->addColumn('user_nama', function ($cuti) {
                    return $cuti->user->nama ?? '-';
                })
                ->editColumn('tanggal_mulai_cuti', function ($cuti) {
                    return Carbon::parse($cuti->tanggal_mulai_cuti)->isoFormat('D MMM YYYY');
                })
                ->editColumn('tanggal_selesai_cuti', function ($cuti) {
                    return Carbon::parse($cuti->tanggal_selesai_cuti)->isoFormat('D MMM YYYY');
                })
                ->addColumn('sisa_hak_cuti', function ($cuti) {
                    return $cuti->user->sisa_hak_cuti ?? '-';
                })
                ->addColumn('aksi', function ($cuti) {
                    $actions = '';
                    // Gunakan Auth::user()->can() untuk memeriksa izin
                    if (Auth::user()->hasPermission('Edit Pengajuan Cuti')) {
                        $editUrl = route('pengajuan_cuti.edit', $cuti->id);
                        $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>';
                    }
                    if (Auth::user()->hasPermission('Hapus Pengajuan Cuti')) {
                        $actions .= '<button type="button" class="text-red-600 hover:text-red-900" title="Hapus" onclick="confirmDelete(' . $cuti->id . ', \'' . e($cuti->nama_cuti) . '\')"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                    }
                    return '<div class="flex space-x-2 justify-center">' . $actions . '</div>';
                })
                ->rawColumns(['jenis_cuti', 'status', 'aksi']) // Kolom yang berisi HTML
                ->make(true);
        }

        // Bagian ini hanya untuk memuat view saat halaman pertama kali dibuka
        return view('pengajuan_cuti.index');
    }

    public function indexPersertujuanCuti(Request $request)
    {
        // Bagian ini akan menangani AJAX request dari DataTables
        if ($request->ajax()) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                $query = Cuti::with('user:id,nama,sisa_hak_cuti');
            } else {
                $query = Cuti::with('user:id,nama,sisa_hak_cuti')->where(function ($q) use ($user) {
                    // Tampilkan data miliknya sendiri
                    $q->where('user_id', $user->id);

                    // DAN data milik bawahannya (jika ada)
                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $q->orWhereIn(
                            'user_id',
                            $user->subordinates()->select('users.id')
                        );
                    }
                });;
            }

            // Proses query menggunakan DataTables
            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom DT_RowIndex
                ->addColumn('user_nama', function ($cuti) {
                    return $cuti->user->nama ?? '-';
                })
                ->editColumn('tanggal_mulai_cuti', function ($cuti) {
                    return Carbon::parse($cuti->tanggal_mulai_cuti)->isoFormat('D MMM YYYY');
                })
                ->editColumn('tanggal_selesai_cuti', function ($cuti) {
                    return Carbon::parse($cuti->tanggal_selesai_cuti)->isoFormat('D MMM YYYY');
                })
                ->addColumn('sisa_hak_cuti', function ($cuti) {
                    return $cuti->user->sisa_hak_cuti ?? '-';
                })
                ->addColumn('aksi', function ($cuti) {
                    $actions = '';
                    // Gunakan Auth::user()->can() untuk memeriksa izin
                    if (Auth::user()->hasPermission('Edit Pengajuan Cuti')) {
                        $editUrl = route('pengajuan_cuti.edit', $cuti->id);
                        $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>';
                    }
                    if (Auth::user()->hasPermission('Hapus Pengajuan Cuti')) {
                        $actions .= '<button type="button" class="text-red-600 hover:text-red-900" title="Hapus" onclick="confirmDelete(' . $cuti->id . ', \'' . e($cuti->nama_cuti) . '\')"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                    }
                    return '<div class="flex space-x-2 justify-center">' . $actions . '</div>';
                })
                ->rawColumns(['jenis_cuti', 'status', 'aksi']) // Kolom yang berisi HTML
                ->make(true);
        }

        // Bagian ini hanya untuk memuat view saat halaman pertama kali dibuka
        return view('persetujuan_cuti.index');
    }

    public function create()
    {
        return view('pengajuan_cuti.create');
    }

    public function store(CutiRequest $request)
    {
        $user = User::select('id', 'nama', 'sisa_hak_cuti')->where('id', Auth::id())->first();

        // Validate leave type
        $jenisCuti = $request->jenis_cuti;
        $isRegularLeave = ($jenisCuti === 'Cuti Biasa');

        // For regular leave, check remaining leave balance
        if ($isRegularLeave) {
            // Calculate number of leave days
            $tanggal_mulai = Carbon::parse($request->tanggal_mulai_cuti);
            $tanggal_selesai = Carbon::parse($request->tanggal_selesai_cuti);
            $jumlah_hari = $tanggal_mulai->diffInDays($tanggal_selesai) + 1; // +1 to include last day

            // Check if remaining leave balance is sufficient
            if ($user->sisa_hak_cuti <= 0) {
                return redirect()->route('pengajuan_cuti.index')
                    ->with('error', 'Cuti biasa hanya bisa diajukan jika masih ada sisa hak cuti');
            }

            if ($user->sisa_hak_cuti < $jumlah_hari) {
                return redirect()->route('pengajuan_cuti.index')
                    ->with('error', 'Sisa hak cuti tidak mencukupi! Anda membutuhkan ' . $jumlah_hari . ' hari, sisa cuti ' . $user->sisa_hak_cuti . ' hari');
            }
        }

        // Handle file upload
        $namaFile = null;
        if ($request->hasFile('foto_cuti') && $request->file('foto_cuti')->isValid()) {
            $foto = $request->file('foto_cuti');
            $namaFile = now()->format('YmdHis') . '.' . $foto->getClientOriginalExtension();
            $path = 'cuti/' . $namaFile;
            Storage::disk('public')->put($path, file_get_contents($foto));
        }

        // Deduct leave balance only for regular leave
        if ($isRegularLeave) {
            $user->decrement('sisa_hak_cuti', $jumlah_hari);
        }

        $data = [
            'nama_cuti' => $request->nama_cuti,
            'jenis_cuti' => $jenisCuti,
            'tanggal_mulai_cuti' => $request->tanggal_mulai_cuti,
            'tanggal_selesai_cuti' => $request->tanggal_selesai_cuti,
            'foto_cuti' => $namaFile,
            'alasan_cuti' => $request->alasan_cuti,
            'status' => 'Menunggu',
            'user_id' => $user->id,
        ];

        Cuti::create($data);

        $users = Auth::user();
        $boss = $users->boss();

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiajukanEmail(
                $boss->nama,
                $user->nama,
                $jenisCuti,
                $request->tanggal_mulai_cuti,
                $request->tanggal_selesai_cuti
            ));
        } else {
            // fallback ke HRD kalau tidak punya boss
            Mail::to($user->email)->send(new PermohonanDiajukanEmail(
                $user->nama,
                $user->nama,
                $jenisCuti,
                $request->tanggal_mulai_cuti,
                $request->tanggal_selesai_cuti
            ));
        }

        return redirect()->route('pengajuan_cuti.index')->with('success', 'Pengajuan Cuti Berhasil Ditambahkan!');
    }

    public function edit(Cuti $cuti)
    {
        if ($cuti->status == 'Disetujui' || $cuti->status == 'Ditolak') {
            return redirect()->route('pengajuan_cuti.index')->with('error', "Cuti Anda sudah dilakukan pesertujuan! Tidak bisa edit");
        }

        $users = User::select('id', 'nama')->get();

        return view('pengajuan_cuti.edit', compact('cuti', 'users'));
    }

    public function update(CutiRequest $request, Cuti $cuti)
    {
        $data = [
            'nama_cuti' => $request->nama_cuti,
            'tanggal_mulai_cuti' => $request->tanggal_mulai_cuti,
            'tanggal_selesai_cuti' => $request->tanggal_selesai_cuti,
            'alasan_cuti' => $request->alasan_cuti,
            'status' => $request->status,
            'alasan_persetujuan_cuti' => $request->alasan_persetujuan_cuti,
        ];

        // Jika ada file foto baru
        if ($request->hasFile('foto_cuti')) {
            // Hapus gambar lama jika ada
            if ($cuti->foto_cuti && Storage::disk('public')->exists('cuti/' . $cuti->foto_cuti)) {
                Storage::disk('public')->delete('cuti/' . $cuti->foto_cuti);
            }

            // Simpan foto baru
            $foto = $request->file('foto_cuti');
            $namaFile = now()->format('YmdHis') . '.' . $foto->getClientOriginalExtension();
            Storage::disk('public')->put('cuti/' . $namaFile, file_get_contents($foto));

            $data['foto_cuti'] = $namaFile;
        }

        $cuti->update($data);

        return redirect()->route('pengajuan_cuti.index')->with('success', 'Pengajuan cuti berhasil diperbarui!');
    }

    public function editPersetujuanCuti(Cuti $cuti)
    {
        $users = User::select('id', 'nama')->get();
        return view('persetujuan_cuti.edit', compact('cuti', 'users'));
    }

    public function updatePersetujuanCuti(CutiRequest $request, Cuti $cuti)
    {
        $data = [
            'status' => $request->status,
            'alasan_persetujuan_cuti' => $request->alasan_persetujuan_cuti,
        ];

        $cuti->update($data);

        // Setelah update status cuti:
        Mail::to($cuti->user->email)->send(new PermohonanDiterimaEmail(
            $cuti->user,
            $cuti->jenis_cuti,
            $request->status,
            $request->alasan_persetujuan_cuti
        ));

        // Kirim juga email ke atasan (jika ada)
        $user = Auth::user();
        $boss = $user->boss(); // method ini harus kamu definisikan

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiketahuiEmail(
                $boss,
                $user,
                $cuti->jenis_cuti,
                $request->status,
                'Permohonan oleh bawahan Anda telah ' . strtolower($request->status)
            ));
        }

        return redirect()->route('persetujuan_cuti.index')->with('success', 'Persetujuan cuti berhasil diperbarui!');
    }

    public function destroy(Cuti $cuti)
    {
        try {
            // Hapus karyawan
            $cuti->delete();

            // Return JSON sukses
            return response()->json([
                'success' => true,
                'message' => "Cuti berhasil dihapus."
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error jika ada data yang terkait (foreign key constraint)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => "Cuti tidak dapat dihapus karena masih memiliki data yang terkait."
                ], 400);
            }

            // Tangani error lain
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan saat menghapus cuti. Silakan coba lagi."
            ], 500);
        }
    }
}
