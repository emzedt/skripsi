<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrashController extends Controller
{
    // Daftar semua model yang menggunakan SoftDeletes
    private $models = [
        'karyawan' => ['Karyawan', \App\Models\User::class, ['id', 'nama', 'email', 'deleted_at']],
        'jabatan' => ['Jabatan', \App\Models\Jabatan::class, ['id', 'nama', 'deleted_at']],
        'cuti' => ['Cuti', \App\Models\Cuti::class, ['id', 'user_id', 'nama_cuti', 'status', 'deleted_at']],
        'absensi' => ['Absensi', \App\Models\Absensi::class, ['id', 'user_id', 'tanggal', 'status', 'deleted_at']],
        'izin' => ['Izin', \App\Models\Izin::class, ['id', 'user_id', 'tanggal', 'jenis_izin', 'deleted_at']],
        'sakit' => ['Sakit', \App\Models\Sakit::class, ['id', 'user_id', 'diagnosa', 'deleted_at']],
        'lembur' => ['Lembur', \App\Models\Lembur::class, ['id', 'user_id', 'upah_lembur_per_jam', 'deleted_at']],
        'people_development' => ['People Development', \App\Models\PeopleDevelopment::class, ['id', 'user_id', 'jabatan', 'deleted_at']],
        'development_kpi' => ['Development KPI', \App\Models\DevelopmentKpi::class, ['id', 'kpi', 'status', 'deleted_at']],
        'development_objective' => ['Development Objective', \App\Models\DevelopmentObjective::class, ['id', 'objective', 'deleted_at']],
        'gaji_bulanan' => ['Gaji Bulanan', \App\Models\GajiBulanan::class, ['id', 'user_id', 'gaji_bulanan', 'deleted_at']],
        'gaji_harian' => ['Gaji Harian', \App\Models\GajiHarian::class, ['id', 'user_id', 'gaji_harian', 'deleted_at']],
        'penggajian' => ['Penggajian', \App\Models\Penggajian::class, ['id', 'user_id', 'periode_mulai', 'gaji_diterima', 'deleted_at']],
        'lokasi' => ['Lokasi', \App\Models\Lokasi::class, ['id', 'nama', 'radius', 'deleted_at']],
        'kalender' => ['Kalender', \App\Models\Kalender::class, ['id', 'tanggal', 'keterangan', 'deleted_at']],
        'hak_cuti' => ['Hak Cuti', \App\Models\HakCuti::class, ['id', 'hak_cuti', 'deleted_at']],
        'absensi_sales' => ['Absensi Sales', \App\Models\AbsensiSales::class, ['id', 'user_id', 'tanggal', 'status_persetujuan', 'deleted_at']],
        'permintaan_lembur' => ['Permintaan Lembur', \App\Models\PermintaanLembur::class, ['id', 'user_id', 'tugas', 'status', 'deleted_at']],
        'status_karyawan' => ['Status Karyawan', \App\Models\StatusKaryawan::class, ['id', 'status_karyawan', 'deleted_at']],
    ];

    // Array $displayColumns sudah dihapus karena redundan

    public function index()
    {
        $data = [];
        foreach ($this->models as $key => $config) {
            $modelClass = $config[1]; // Mengambil Class Name
            $data[$key] = [
                'label' => $config[0], // Mengambil Label
                'total' => $modelClass::onlyTrashed()->count(),
                'columns' => $config[2], // Mengambil daftar kolom
            ];
        }
        return view('trash.index', ['data' => $data]);
    }

    public function getData(Request $request)
    {
        $modelKey = $request->query('model');

        if (!array_key_exists($modelKey, $this->models)) {
            abort(404, 'Model tidak ditemukan.');
        }

        $config = $this->models[$modelKey];
        $modelClass = $config[1]; // Mengambil class dari config

        $query = $modelClass::onlyTrashed();

        // Cek jika model memiliki relasi 'user' untuk eager loading
        if (method_exists(new $modelClass, 'user')) {
            $query->with('user:id,nama');
        }

        return DataTables::of($query)
            ->editColumn('deleted_at', fn($item) => $item->deleted_at ? Carbon::parse($item->deleted_at)->format('d M Y H:i:s') : '')
            ->addColumn('aksi', function ($item) use ($modelKey) {
                // ... (kode aksi Anda tetap sama)
                $restoreUrl = route('trash.restore', ['model' => $modelKey, 'id' => $item->id]);
                $showUrl = route('trash.show', ['model' => $modelKey, 'id' => $item->id]);
                $modelNameLabel = Str::title(str_replace('_', ' ', Str::singular($modelKey)));
                $tableId = "#trash-table-{$modelKey}";
                $restoreIcon = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M4.52185 7H7C7.55229 7 8 7.44772 8 8C8 8.55229 7.55228 9 7 9H3C1.89543 9 1 8.10457 1 7V3C1 2.44772 1.44772 2 2 2C2.55228 2 3 2.44772 3 3V5.6754C4.26953 3.8688 6.06062 2.47676 8.14852 1.69631C10.6633 0.756291 13.435 0.768419 15.9415 1.73041C18.448 2.69239 20.5161 4.53782 21.7562 6.91897C22.9963 9.30013 23.3228 12.0526 22.6741 14.6578C22.0254 17.263 20.4464 19.541 18.2345 21.0626C16.0226 22.5842 13.3306 23.2444 10.6657 22.9188C8.00083 22.5931 5.54702 21.3041 3.76664 19.2946C2.20818 17.5356 1.25993 15.3309 1.04625 13.0078C0.995657 12.4579 1.45216 12.0088 2.00445 12.0084C2.55673 12.0079 3.00351 12.4566 3.06526 13.0055C3.27138 14.8374 4.03712 16.5706 5.27027 17.9625C6.7255 19.605 8.73118 20.6586 10.9094 20.9247C13.0876 21.1909 15.288 20.6513 17.0959 19.4075C18.9039 18.1638 20.1945 16.3018 20.7247 14.1724C21.2549 12.043 20.9881 9.79319 19.9745 7.8469C18.9608 5.90061 17.2704 4.3922 15.2217 3.6059C13.173 2.8196 10.9074 2.80968 8.8519 3.57803C7.11008 4.22911 5.62099 5.40094 4.57993 6.92229C4.56156 6.94914 4.54217 6.97505 4.52185 7Z"></path></g></svg>';
                $showIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';

                $restoreButton = '<button type="button" onclick="confirmRestore(\'' . $restoreUrl . '\', \'' . $modelNameLabel . '\', \'' . $tableId . '\', \'' . $modelKey . '\')" class="text-blue-600 hover:text-blue-900 font-semibold">' . $restoreIcon . '</button>';
                $showLink = '<a href="' . $showUrl . '" class="text-black hover:text-gray-600" title="Lihat Detail">' . $showIcon . '</a>';

                return '<div class="flex items-center space-x-4">' . $showLink . $restoreButton . '</div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function restore(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) {
            return response()->json(['success' => false, 'message' => 'Model tidak valid'], 404);
        }

        // PERBAIKAN: Ambil nama class dari indeks ke-1
        $modelClass = $this->models[$model][1];

        $record = $modelClass::onlyTrashed()->findOrFail($id);
        $record->restore();

        if ($request->wantsJson()) {
            $newTotal = $modelClass::onlyTrashed()->count();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dipulihkan.',
                'newTotal' => $newTotal
            ]);
        }

        return redirect()->route('trash.index', ['tab' => $model])
            ->with('success', 'Data berhasil dipulihkan.');
    }

    public function show(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }

        $modelClass = $this->models[$model][1];

        $record = $modelClass::onlyTrashed()->findOrFail($id);

        return view('trash.show', compact('record', 'model'));
    }
}
