<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiSales;
use App\Models\GajiBulanan;
use App\Models\GajiHarian;
use App\Models\Izin;
use App\Models\Kalender;
use App\Models\Lembur;
use App\Models\Penggajian;
use App\Models\User;
use App\Models\PermintaanLembur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;

class PenggajianController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Penggajian::with('user:id,nama');

            // Terapkan logika hak akses yang benar
            $user = Auth::user();

            if (!$user->isAdmin() && !$user->isHRD()) {
                $query->where(function ($q) use ($user) {
                    // PERBAIKAN: Seharusnya memfilter berdasarkan 'user_id' di tabel penggajian
                    $q->where('user_id', $user->id);

                    // Jika user adalah manajer, tampilkan juga data bawahannya
                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $subordinateIds = $user->subordinates()->pluck('users.id');
                        $q->orWhereIn('user_id', $subordinateIds);
                    }
                });
            }

            return DataTables::of($query)
                ->addColumn('periode', function ($gaji) {
                    $mulai = Carbon::parse($gaji->periode_mulai)->format('d M Y');
                    $selesai = Carbon::parse($gaji->periode_selesai)->format('d M Y');
                    return "{$mulai} - {$selesai}";
                })
                ->editColumn('gaji_diterima', fn($gaji) => 'Rp ' . number_format($gaji->gaji_diterima, 0, ',', '.'))
                ->editColumn('lembur', fn($gaji) => 'Rp ' . number_format($gaji->lembur, 0, ',', '.'))
                ->editColumn('potongan_gaji', fn($gaji) => 'Rp ' . number_format($gaji->potongan_gaji, 0, ',', '.'))
                ->addColumn('aksi', function ($gaji) {
                    $user = Auth::user();
                    $showUrl = route('penggajian.show', $gaji->id);
                    $editUrl = route('penggajian.edit', $gaji->id);
                    $periode = Carbon::parse($gaji->periode_mulai)->format('M Y');
                    $deleteFunc = "confirmDelete({$gaji->id}, '{$gaji->user->nama}', '{$periode}')";

                    $showIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
                    $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
                    $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';

                    $actions = '<div class="flex space-x-2 justify-center">';
                    $actions .= '<a href="' . $showUrl . '" class="text-black hover:text-gray-600"  title="Lihat Slip">' . $showIcon . '</a>';

                    if ($user->hasPermission('Edit Penggajian')) {
                        $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">' . $editIcon . '</a>';
                    }
                    if ($user->hasPermission('Hapus Penggajian')) {
                        $actions .= '<button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('penggajian.index');
    }

    public function create()
    {
        $users = User::with(['statusKaryawan', 'gajiBulanan', 'gajiHarian', 'lembur'])
            ->where(function ($query) {
                $query->whereHas('gajiBulanan')
                    ->orWhereHas('gajiHarian');
            })
            ->get();

        return view('penggajian.create', compact('users'));
    }

    protected function isHariLibur($tanggal)
    {
        return Kalender::where('tanggal', $tanggal)->exists();
    }

    protected function hitungJumlahHariKerja($periodeMulai, $periodeSelesai, $userId)
    {
        $mulai = new \DateTime($periodeMulai);
        $selesai = new \DateTime($periodeSelesai);
        $selesai->modify('+1 day');
        $jumlahHariKerja = 0;
        $interval = new \DateInterval('P1D');
        $rentangTanggal = new \DatePeriod($mulai, $interval, $selesai);
        $user = User::findOrFail($userId);

        foreach ($rentangTanggal as $tanggalObj) {
            $tanggal = $tanggalObj->format('Y-m-d');

            if ($this->isHariLibur($tanggal)) {
                continue;
            }

            if ($user->isKaryawanHarian()) {
                // Untuk karyawan harian, hitung hanya jika ada catatan absensi masuk pada tanggal tersebut
                if (Absensi::where('user_id', $userId)
                    ->whereDate('tanggal', $tanggal)
                    ->whereNotNull('jam_masuk')
                    ->whereIn('status', ['Hadir', 'Telat'])
                    ->exists()
                ) {
                    $jumlahHariKerja++;
                }
            } else {
                // Untuk karyawan tetap, hitung semua hari di luar hari libur
                $jumlahHariKerja++;
            }
        }

        // Kurangkan hari izin dari total hari kerja
        $izins = Izin::where('user_id', $userId)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai]) // Gunakan tanggal_mulai
            ->get();

        foreach ($izins as $izin) {
            $tanggalIzin = $izin->tanggal->format('Y-m-d'); // Gunakan tanggal_mulai
            if ($izin->jenis_izin == 'Satu Hari') {
                $jumlahHariKerja -= 1;
            } elseif ($izin->jenis_izin == 'Setengah Hari Pagi' || $izin->jenis_izin == 'Setengah Hari Siang') {
                if (isset($setengahHariCounter[$tanggalIzin])) {
                    $jumlahHariKerja -= 0.5;
                    $setengahHariCounter[$tanggalIzin] = true; // Tandai sudah ada setengah hari lain
                } else {
                    $setengahHariCounter[$tanggalIzin] = false; // Pertama kali setengah hari
                }
            }
        }

        if ($user->hasPermission('Tambah Absensi')) {
            $jumlahAlfa = Absensi::where('user_id', $userId)
                ->where('status', 'Alfa')
                ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
                ->count();

            $jumlahHariKerja -= $jumlahAlfa;
        } else {
            $periode = CarbonPeriod::create($periodeMulai, $periodeSelesai);

            $jumlahAlfaSales = 0;

            foreach ($periode as $tanggal) {
                $absensiHariIni = AbsensiSales::whereDate('tanggal', $tanggal)
                    ->where('user_id', $userId)
                    ->whereNull('deleted_at') // pastikan yang aktif
                    ->get();

                if ($absensiHariIni->isEmpty()) {
                    // Tidak ada absensi sama sekali → Alfa
                    $jumlahAlfaSales++;
                } elseif ($absensiHariIni->every(fn($a) => $a->status_persetujuan === 'Ditolak')) {
                    // Ada, tapi semuanya ditolak → Alfa
                    $jumlahAlfaSales++;
                }
            }

            $jumlahHariKerja -= $jumlahAlfaSales;
        }

        return $jumlahHariKerja;
    }

    public function store(Request $request)
    {
        // 1️⃣ Validasi input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode_mulai' => 'required|date|date_format:Y-m-d',
            'periode_selesai' => 'required|date|date_format:Y-m-d|after_or_equal:periode_mulai',
        ]);

        // 2️⃣ Ambil user lengkap
        $user = User::with(['gajiBulanan', 'gajiHarian', 'lembur'])->findOrFail($validated['user_id']);

        // 3️⃣ Hitung total lembur
        $lemburRequests = PermintaanLembur::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal_mulai', [$validated['periode_mulai'], $validated['periode_selesai']])
            ->get();

        $totalUangLembur = 0;
        foreach ($lemburRequests as $lemburRequest) {
            $tanggalLembur = $lemburRequest->tanggal_mulai;

            // ⚠️ PERBAIKAN: Hanya cek di tabel 'absensis'
            $wasPresent = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggalLembur)
                ->whereIn('status', ['Hadir', 'Telat'])
                ->exists();

            if ($wasPresent) {
                $jamLembur = $lemburRequest->lama_lembur / 60;
                $normalJam = min($jamLembur, 5);
                $extraJam = max($jamLembur - 5, 0);

                $upahLemburPerJam = $user->lembur->upah_lembur_per_jam ?? 0;
                $upahLemburOver5Jam = $user->lembur->upah_lembur_over_5_jam ?? 0;

                $pay = ($normalJam * $upahLemburPerJam) + ($extraJam * $upahLemburOver5Jam);
                $totalUangLembur += $pay;
            }
        }

        // 4️⃣ Hitung izin
        $izinDisetujui = Izin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal', [$validated['periode_mulai'], $validated['periode_selesai']])
            ->get();

        $jumlahIzinSatuHari = $izinDisetujui->where('jenis_izin', 'Satu Hari')->count();
        $jumlahSetengahHari = $izinDisetujui->whereIn('jenis_izin', ['Setengah Hari Pagi', 'Setengah Hari Siang'])->count();
        $potonganDariSetengahHari = (int)($jumlahSetengahHari / 2);
        $totalPotonganIzin = $jumlahIzinSatuHari + $potonganDariSetengahHari;

        // 5️⃣ Hitung alfa
        $jumlahAlfa = Absensi::where('user_id', $user->id)
            ->where('status', 'Alfa')
            ->whereBetween('tanggal', [$validated['periode_mulai'], $validated['periode_selesai']])
            ->count();

        // 6️⃣ Total hari kerja ideal di periode ini (exclude hari libur)
        $periode = CarbonPeriod::create($validated['periode_mulai'], $validated['periode_selesai']);
        $totalHariKerjaIdeal = 0;
        foreach ($periode as $tanggal) {
            if (!Kalender::where('tanggal', $tanggal->format('Y-m-d'))->exists()) {
                $totalHariKerjaIdeal++;
            }
        }

        // 7️⃣ Hitung jumlah hari hadir (benar-benar hadir)
        $jumlahHariHadir = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$validated['periode_mulai'], $validated['periode_selesai']])
            ->whereIn('status', ['Hadir', 'Telat'])
            ->count();

        // 8️⃣ Hitung gaji & potongan
        $gajiPokok = 0;
        $uangMakan = 0;
        $potonganGaji = 0;
        $gajiDiterima = 0;

        if ($user->isKaryawanHarian() && $user->gajiHarian) {
            // Asumsi:
            // - Gaji Harian = gaji per hari
            // - Uang Makan = upah makan per hari
            // - Total Hari Kerja yang dibayar = jumlah hari hadir
            $gajiPokok = $jumlahHariHadir * ($user->gajiHarian->gaji_harian ?? 0);
            $uangMakan = $jumlahHariHadir * ($user->gajiHarian->upah_makan_harian ?? 0);

            // Perhitungan potongan didasarkan pada ketidakhadiran (izin dan alfa)
            $jumlahHariTidakHadir = $totalPotonganIzin + $jumlahAlfa;
            // Potongan dihitung berdasarkan gaji harian
            $potonganGaji = $jumlahHariTidakHadir * ($user->gajiHarian->gaji_harian ?? 0);

            // Total gaji yang diterima = (Gaji Pokok + Uang Makan + Uang Lembur) - Potongan
            $gajiDiterima = ($gajiPokok + $uangMakan + $totalUangLembur) - $potonganGaji;
        } elseif ($user->isKaryawanTetap() && $user->gajiBulanan) {
            $hariKerjaEfektif = 22;
            $gajiPerHari = $user->gajiBulanan->gaji_bulanan / $hariKerjaEfektif;
            $potonganGaji = ($jumlahAlfa + $totalPotonganIzin) * $gajiPerHari;
            $gajiPokok = $user->gajiBulanan->gaji_bulanan - $potonganGaji;
            $gajiDiterima = $gajiPokok + $totalUangLembur; // Asumsi gaji tetap tidak ada uang makan harian terpisah
        }

        // 9️⃣ Simpan data
        Penggajian::create([
            'user_id' => $user->id,
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'gaji_diterima' => $gajiDiterima,
            'lembur' => $totalUangLembur,
            'potongan_gaji' => $potonganGaji,
        ]);

        return redirect()->route('penggajian.index')
            ->with('success', 'Penggajian berhasil dibuat');
    }



    public function show(Penggajian $penggajian)
    {
        $penggajian->load(['user', 'user.gajiBulanan', 'user.gajiHarian', 'user.lembur']);

        $totalJamLembur = 0;
        if ($penggajian->user->lembur && $penggajian->user->lembur->upah_lembur_per_jam > 0) {
            $totalJamLembur = ceil($penggajian->lembur / $penggajian->user->lembur->upah_lembur_per_jam);
        }

        return view('penggajian.show', compact('penggajian', 'totalJamLembur'));
    }

    public function slip(Penggajian $penggajian, GajiHarian $gajiHarian, GajiBulanan $gajiBulanan)
    {
        // Calculate overtime data
        $overtimeOver5Hours = $penggajian->user->permintaanLembur
            ->where('status', 'Disetujui')
            ->filter(fn($lembur) => $lembur->lama_lembur > 300)
            ->count();

        $totalJamLembur = $penggajian->user->permintaanLembur
            ->where('status', 'Disetujui')
            ->sum('lama_lembur');

        // Prepare data for PDF
        $data = [
            'penggajian' => $penggajian,
            'overtimeOver5Hours' => $overtimeOver5Hours,
            'totalJamLembur' => $totalJamLembur,
            // 'uangLembur' => $uangLembur,
            'tanggalCetak' => now()->format('d F Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('penggajian.slip', $data);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Download PDF with filename
        return $pdf->download('slip-gaji-' . $penggajian->user->nama . '-' . $penggajian->periode_mulai->format('Y-m') . '.pdf');
    }

    public function edit(Penggajian $penggajian)
    {
        $users = User::with(['statusKaryawan', 'gajiBulanan', 'gajiHarian'])
            ->where(function ($query) {
                $query->whereHas('gajiBulanan')
                    ->orWhereHas('gajiHarian');
            })
            ->get();

        return view('penggajian.edit', compact('penggajian', 'users'));
    }

    public function update(Request $request, Penggajian $penggajian)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'gaji_diterima' => 'required|numeric|min:0',
            'lembur' => 'required|numeric|min:0',
            'potongan_gaji' => 'required|numeric|min:0'
        ]);

        $penggajian->update($request->all());

        return redirect()->route('penggajian.index')
            ->with('success', 'Penggajian berhasil diperbarui');
    }

    public function destroy(Penggajian $penggajian)
    {
        try {
            // Ini akan menjalankan soft delete jika Trait sudah ditambahkan di Model
            $penggajian->delete();

            // Kembalikan respons JSON dengan status sukses
            return response()->json([
                'success' => true,
                'message' => 'Data penggajian berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            // (Opsional tapi bagus) Menangani jika ada error saat proses hapus
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data. Terjadi kesalahan server.'
            ], 500);
        }
    }

    public function slipGaji(Penggajian $penggajian)
    {
        $penggajian->load(['user', 'user.gajiBulanan', 'user.gajiHarian', 'user.lembur']);

        // Hitung total jam lembur untuk periode ini
        $totalJamLembur = ceil($penggajian->lembur / ($penggajian->user->lembur->upah_lembur_per_jam ?? 1));

        return view('penggajian.slip', compact('penggajian', 'totalJamLembur'));
    }
}
