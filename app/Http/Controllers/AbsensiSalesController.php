<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsensiSalesRequest;
use App\Models\AbsensiSales;
use App\Models\Kalender;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class AbsensiSalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Mengambil data dengan relasi user untuk menampilkan nama
            $user = Auth::user();

            if ($user->isAdmin() || $user->isHRD()) {
                $query = AbsensiSales::with('user:id,nama')->select('absensi_sales.*');
            } else {
                $query = AbsensiSales::with('user:id,nama')->select('absensi_sales.*')->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);

                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $query->orWhereIn(
                            'user_id',
                            $user->subordinates()->select('users.id')
                        );
                    }
                });
            }


            return DataTables::of($query)
                ->editColumn('tanggal', function ($absensi) {
                    return Carbon::parse($absensi->tanggal)->format('d M Y');
                })
                ->editColumn('jam', function ($absensi) {
                    return Carbon::parse($absensi->jam)->format('H:i');
                })
                ->addColumn('aksi', function ($absensi) {
                    // Membuat tombol aksi (Show, Edit, Delete)
                    $showUrl = route('absensi_sales.show', $absensi->id);
                    $editUrl = route('absensi_sales.edit', $absensi->id);
                    $deleteFunc = "confirmDelete({$absensi->id}, '{$absensi->user->nama}', '" . Carbon::parse($absensi->tanggal)->format('d-m-Y') . "')";

                    $showIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
                    $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
                    $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';

                    return '
                        <div class="flex space-x-2">
                            <a href="' . $showUrl . '" class="text-black hover:text-gray-900" title="Lihat">' . $showIcon . '</a>
                            <a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">' . $editIcon . '</a>
                            <button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>
                        </div>
                    ';
                })
                ->rawColumns(['status_persetujuan', 'aksi'])
                ->make(true);
        }

        return view('absensi_sales.index');
    }

    public function create()
    {
        $today = now()->toDateString();
        $isHoliday = Kalender::whereDate('tanggal', $today)->first();
        if ($isHoliday->jenis_libur === 'Cuti Bersama' ||  $isHoliday->jenis_libur === 'Libur') {
            return redirect()->route('absensi_sales.index')->with('error', 'Anda tidak bisa absen karena lagi libur atau cuti bersama!');
        }

        return view('absensi_sales.create');
    }

    public function show(AbsensiSales $absensiSales)
    {
        return view('absensi_sales.show', compact('absensiSales'));
    }

    public function store(AbsensiSalesRequest $request)
    {
        try {
            $fotoBase64 = $request->input('foto_base64');
            $namaFile = null;

            if ($fotoBase64) {
                // Pisahkan metadata dari base64 string
                [$type, $fotoData] = explode(';', $fotoBase64);
                [, $fotoData] = explode(',', $fotoData);

                $fotoData = base64_decode($fotoData);
                $namaFile = now()->format('YmdHis') . '.jpg';
                $path = 'absensi_sales/' . $namaFile;
                Storage::disk('public')->put($path, $fotoData);
            }

            $data = [
                'tanggal' => $request->tanggal,
                'foto' => $path,
                'jam' => $request->jam,
                'deskripsi' => $request->deskripsi,
                'status' => $request->status,
                'status_persetujuan' => 'Menunggu',
                'user_id' => Auth::id()
            ];
            $absensi = AbsensiSales::create($data);

            return redirect()->route('absensi_sales.index')
                ->with('success', 'Absensi sales berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', [
                    'title' => 'Gagal!',
                    'text' => 'Gagal menyimpan absensi: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
        }
    }

    public function edit(AbsensiSales $absensiSales)
    {
        return view('absensi_sales.edit', compact('absensiSales'));
    }

    public function update(AbsensiSalesRequest $request, AbsensiSales $absensiSales)
    {
        $data = [
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
            'status_persetujuan' => $request->status_persetujuan,
            'user_id' => Auth::user()->id
        ];

        if ($request->hasFile('foto')) {
            if ($absensiSales->foto && Storage::disk('public')->exists('absensi_sales/' . $absensiSales->foto)) {
                Storage::disk('public')->delete('absensi_sales/' . $absensiSales->foto);
            }
            $foto = $request->file('foto');
            $namaFile = now()->format('YmdHis') . '.' . $foto->getClientOriginalExtension();
            Storage::disk('public')->put('absensi_sales/' . $namaFile, file_get_contents($foto));

            $data['foto'] = $namaFile;
        }

        $absensiSales->update($data);

        return redirect()->route('absensi_sales.index')->with('success', 'Absensi sales berhasil diperbarui!');
    }

    public function destroy(AbsensiSales $absensiSales)
    {
        try {
            // Hapus karyawan
            $absensiSales->delete();

            // Return JSON sukses
            return response()->json([
                'success' => true,
                'message' => "Absensi sales berhasil dihapus."
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error jika ada data yang terkait (foreign key constraint)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => "Karyawan tidak dapat dihapus karena masih memiliki data yang terkait."
                ], 400);
            }

            // Tangani error lain
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan saat menghapus karyawan. Silakan coba lagi."
            ], 500);
        }
    }
}
