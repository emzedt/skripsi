<?php

namespace App\Http\Controllers;

use App\Http\Requests\LokasiRequest;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek jika permintaan datang dari AJAX (DataTables)
        if ($request->ajax()) {

            // 2. Buat query dasar, jangan eksekusi dengan ->get() atau ->paginate()
            $query = Lokasi::query()->select(['id', 'nama', 'longitude', 'latitude', 'radius']);

            return DataTables::of($query)
                ->addColumn('aksi', function ($lokasi) {
                    // 3. Tambahkan kolom "Aksi" dengan tombol Edit dan Hapus
                    $editUrl = route('lokasi.edit', $lokasi->id);
                    $deleteButton = '<button type="button" onclick="confirmDelete(' . $lokasi->id . ', \'' . htmlspecialchars($lokasi->nama, ENT_QUOTES) . '\')" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>';
                    $editLink = '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>';

                    return '<div class="flex space-x-2">' . $editLink . $deleteButton . '</div>';
                })
                ->rawColumns(['aksi']) // 4. Izinkan HTML pada kolom 'aksi'
                ->make(true); // 5. Proses dan kirim response JSON
        }

        // 6. Untuk permintaan biasa (bukan AJAX), cukup tampilkan view-nya
        return view('lokasi.index');
    }

    public function create()
    {
        $lokasis = Lokasi::select('id', 'nama')
            ->get();

        return view('lokasi.create', compact('lokasis'));
    }

    public function store(LokasiRequest $request)
    {
        $data = [
            'nama' => $request->nama,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius
        ];

        Lokasi::create($data);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(LokasiRequest $request, Lokasi $lokasi)
    {
        $data = [
            'nama' => $request->nama,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius
        ];

        $lokasi->update($data);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui!');
    }

    public function destroy(Lokasi $lokasi)
    {
        try {
            $lokasi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lokasi berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus lokasi.'
            ], 500);
        }
    }
}
