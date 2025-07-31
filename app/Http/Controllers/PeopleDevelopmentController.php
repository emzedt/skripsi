<?php

namespace App\Http\Controllers;

use App\Models\PeopleDevelopment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PeopleDevelopmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Menggunakan withCount untuk efisiensi query, dan with untuk relasi jabatan
            $query = User::with('jabatan:id,nama')
                ->withCount('peopleDevelopment');;

            // Terapkan logika hak akses
            $currentUser = Auth::user();
            if (!$currentUser->isAdmin() && !$currentUser->isHRD()) {
                $query->where(function ($q) use ($currentUser) {
                    // Hanya user itu sendiri
                    $q->where('id', $currentUser->id);

                    // atau bawahan jika user adalah seorang manajer
                    if ($currentUser->jabatan && $currentUser->jabatan->childJabatans()->exists()) {
                        $subordinateIds = $currentUser->subordinates()->pluck('users.id');
                        $q->orWhereIn('id', $subordinateIds);
                    }
                });
            }

            return DataTables::of($query)
                ->make(true);
        }

        return view('people_development.index');
    }

    /**
     * Show employee's development records
     */
    public function show(User $user)
    {
        $user->load(['peopleDevelopment.objectives.kpis']);

        return view('people_development.show', compact('user'));
    }

    public function create(User $user)
    {
        return view('people_development.create', compact('user'));
    }

    /**
     * Store new development record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date',
            'jabatan' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'objectives' => 'required|array|min:1',
            'objectives.*.name' => 'required|string|max:255',
            'objectives.*.kpis' => 'required|array|min:1',
            'objectives.*.kpis.*.name' => 'required|string|max:255',
            'objectives.*.kpis.*.tipe_kpi' => 'required|string',
            'objectives.*.kpis.*.target' => 'required|numeric',
            'objectives.*.kpis.*.realisasi' => 'required|numeric',
            'objectives.*.kpis.*.bobot' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $development = PeopleDevelopment::create([
                'user_id' => $validated['user_id'],
                'periode_mulai' => $validated['periode_mulai'],
                'periode_selesai' => $validated['periode_selesai'],
                'jabatan' => $validated['jabatan'],
                'keterangan' => $validated['keterangan']
            ]);

            foreach ($validated['objectives'] as $objectiveData) {
                $objective = $development->objectives()->create([
                    'objective' => $objectiveData['name']
                ]);

                foreach ($objectiveData['kpis'] as $kpiData) {
                    $objective->kpis()->create([
                        'kpi' => $kpiData['name'],
                        'tipe_kpi' => $kpiData['tipe_kpi'],
                        'target' => $kpiData['target'],
                        'realisasi' => $kpiData['realisasi'],
                        'bobot' => $kpiData['bobot'],
                        'status' => $kpiData['realisasi'] >= $kpiData['target'] ? 'Tercapai' : 'Tidak Tercapai'
                    ]);
                }
            }

            return redirect()->route('people_development.show', $validated['user_id'])
                ->with('success', 'People Development Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    public function edit(User $user, PeopleDevelopment $development)
    {
        return view('people_development.edit', compact('user', 'development'));
    }

    public function update(Request $request, User $user, PeopleDevelopment $development)
    {
        $validated = $request->validate([
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'jabatan' => 'required|string|max:255',
            'objectives' => 'required|array',
            'objectives.*.id' => 'nullable|integer', // Tambahkan ini untuk identifikasi Objective yang sudah ada
            'objectives.*.name' => 'required|string',
            'objectives.*.kpis' => 'required|array',
            'objectives.*.kpis.*.id' => 'nullable|integer',  // Tambahkan ini untuk identifikasi KPI yang sudah ada
            'objectives.*.kpis.*.name' => 'required|string',
            'objectives.*.kpis.*.tipe_kpi' => 'required|string',
            'objectives.*.kpis.*.target' => 'required|numeric',
            'objectives.*.kpis.*.realisasi' => 'required|numeric',
            'objectives.*.kpis.*.bobot' => 'required|numeric|min:0|max:100',
            'keterangan' => 'required|string',
        ]);

        // Update the development record
        $development->update([
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'jabatan' => $validated['jabatan'],
            'keterangan' => $validated['keterangan'],
        ]);

        // Handle objectives and KPIs update/create
        $objectiveIdsToKeep = []; // Array untuk menyimpan ID Objective yang tidak dihapus
        foreach ($validated['objectives'] as $objectiveData) {
            if (isset($objectiveData['id'])) {
                // Objective sudah ada, update
                $objective = $development->objectives()->findOrFail($objectiveData['id']); //gunakan findOrFail
                $objective->update([
                    'objective' => $objectiveData['name'],
                ]);
                $objectiveIdsToKeep[] = $objective->id; // Simpan ID Objective
            } else {
                // Objective baru, buat
                $objective = $development->objectives()->create([
                    'objective' => $objectiveData['name'],
                ]);
                $objectiveIdsToKeep[] = $objective->id; // Simpan ID Objective
            }

            $kpiIdsToKeep = []; // Array untuk menyimpan ID KPI yang tidak dihapus
            foreach ($objectiveData['kpis'] as $kpiData) {
                if (isset($kpiData['id'])) {
                    // KPI sudah ada, update
                    $kpi = $objective->kpis()->findOrFail($kpiData['id']); //gunakan findOrFail
                    $kpi->update([
                        'kpi' => $kpiData['name'],
                        'tipe_kpi' => $kpiData['tipe_kpi'],
                        'target' => $kpiData['target'],
                        'realisasi' => $kpiData['realisasi'],
                        'bobot' => $kpiData['bobot'],
                    ]);
                    $kpiIdsToKeep[] = $kpi->id; // Simpan ID KPI
                } else {
                    // KPI baru, buat
                    $kpi = $objective->kpis()->create([
                        'kpi' => $kpiData['name'],
                        'tipe_kpi' => $kpiData['tipe_kpi'],
                        'target' => $kpiData['target'],
                        'realisasi' => $kpiData['realisasi'],
                        'bobot' => $kpiData['bobot'],
                    ]);
                    $kpiIdsToKeep[] = $kpi->id; // Simpan ID KPI
                }
            }
            // Hapus KPI yang tidak ada di data yang dikirim
            $objective->kpis()->whereNotIn('id', $kpiIdsToKeep)->delete();
        }

        // Hapus Objective yang tidak ada di data yang dikirim
        $development->objectives()->whereNotIn('id', $objectiveIdsToKeep)->delete();

        return redirect()->route('people_development.show', $user)
            ->with('success', 'People Development Berhasil Diperbarui!');
    }

    /**
     * Delete development record
     */
    public function destroy(User $user, PeopleDevelopment $development)
    {
        $development->delete();

        return redirect()->route('people_development.show', $user)
            ->with('success', 'People Development Berhasil Dihapus!');
    }
}
