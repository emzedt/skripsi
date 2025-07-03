<?php

namespace App\Http\Controllers;

use App\Models\StatusKaryawan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StatusKaryawanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek apakah ini permintaan AJAX dari DataTables
        if ($request->ajax()) {

            // 2. Buat query dasar (menggunakan query builder, bukan ->get() atau ->paginate())
            $query = StatusKaryawan::query()->select(['id', 'status_karyawan']);

            return DataTables::of($query)
                ->addColumn('aksi', function ($status) {
                    // 3. Tambahkan kolom "Aksi" secara dinamis
                    $editUrl = route('status_karyawan.edit', $status->id);
                    $deleteButton = '<button type="button" onclick="confirmDelete(' . $status->id . ', \'' . htmlspecialchars($status->status_karyawan, ENT_QUOTES) . '\')" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>';
                    $editLink = '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>';

                    return '<div class="flex space-x-2">' . $editLink . $deleteButton . '</div>';
                })
                ->rawColumns(['aksi']) // 4. Beritahu DataTables bahwa kolom 'aksi' berisi HTML
                ->make(true); // 5. Proses dan kembalikan data dalam format JSON
        }

        // 6. Untuk permintaan biasa (saat pertama kali membuka halaman), cukup tampilkan view-nya
        return view('status_karyawan.index');
    }

    public function create()
    {
        $statusKaryawans = StatusKaryawan::select('id', 'status_karyawan')->get();

        return view('status_karyawan.create', compact('statusKaryawans'));
    }

    public function store(Request $request)
    {
        $data = [
            'status_karyawan' => $request->status_karyawan
        ];

        StatusKaryawan::create($data);

        return redirect()->route('status_karyawan.index')->with('success', 'Status karyawan berhasil ditambahkan!');
    }

    public function edit(StatusKaryawan $statusKaryawan)
    {
        return view('status_karyawan.edit', compact('statusKaryawan'));
    }

    public function update(Request $request, StatusKaryawan $statusKaryawan)
    {
        $data = [
            'status_karyawan' => $request->status_karyawan
        ];

        $statusKaryawan->update($data);

        return redirect()->route('status_karyawan.index')->with('success', 'Status karyawan berhasil diperbarui!');
    }

    public function destroy(StatusKaryawan $statusKaryawan)
    {
        try {
            $statusKaryawan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Status karyawan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus status karyawan.'
            ], 500);
        }
    }
}
