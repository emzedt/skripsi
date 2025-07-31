<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiSales;
use App\Models\Cuti;
use App\Models\PermintaanLembur;
use App\Models\Sakit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', now()->format('Y-m'));
        $userId = Auth::id();
        $carbonBulan = Carbon::parse($bulan)->startOfMonth();
        $tanggalAwal = $carbonBulan->copy()->startOfMonth();
        $tanggalAkhir = $carbonBulan->copy()->endOfMonth();

        $users = Auth::user()->isAdmin() ? User::all() : [];

        $absenMasuk = collect();
        $alfa = collect();
        $alfaDates = [];
        $jumlahAlfaSeluruhUser = 0;

        // Ambil data absen user login
        if (Auth::user()->hasPermission('Tambah Absensi')) {
            $absenMasuk = Absensi::where('user_id', $userId)
                ->whereIn('status', ['Hadir', 'Telat'])
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->get();

            $alfa = Absensi::where('user_id', $userId)
                ->where('status', 'Alfa')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->get();

            $alfaDates = $alfa->pluck('tanggal')->toArray();
        } elseif (Auth::user()->hasPermission('Tambah Absensi Sales')) {
            $absenMasuk = AbsensiSales::where('user_id', $userId)
                ->where('status_persetujuan', 'Disetujui')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->get();

            $allDates = [];
            for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
                if ($date->isWeekday()) {
                    $allDates[] = $date->format('Y-m-d');
                }
            }

            $hadirDates = $absenMasuk->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
            $alfaDates = array_values(array_diff($allDates, $hadirDates));
            $alfa = collect($alfaDates);
        }

        $userIds = collect([$userId]);
        if (Auth::user()->isAdmin()) {
            $userIds = User::pluck('id');
        }
        // elseif (Auth::user()->subordinates()->exists()) {
        //     $userIds = Auth::user()->subordinates()->pluck('id')->push($userId);
        // }

        // Hitung alfa hanya dari catatan status = 'Alfa'
        $jumlahAlfaSeluruhUser = Absensi::whereIn('user_id', $userIds)
            ->where('status', 'Alfa')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->count();

        $sakit = Sakit::whereBetween('tanggal_mulai', [$tanggalAwal, $tanggalAkhir])
            ->where('status', 'Disetujui')
            ->when(!Auth::user()->isAdmin(), function ($query) use ($userIds) {
                return $query->whereIn('user_id', $userIds);
            })->get();

        $lembur = PermintaanLembur::whereBetween('tanggal_mulai', [$tanggalAwal, $tanggalAkhir])
            ->where('status', 'Disetujui')
            ->when(!Auth::user()->isAdmin(), function ($query) use ($userIds) {
                return $query->whereIn('user_id', $userIds);
            })->get();

        $cuti = Cuti::whereBetween('tanggal_mulai_cuti', [$tanggalAwal, $tanggalAkhir])
            ->where('status', 'Disetujui')
            ->when(!Auth::user()->isAdmin(), function ($query) use ($userIds) {
                return $query->whereIn('user_id', $userIds);
            })->get();

        return view('dashboard', compact(
            'users',
            'absenMasuk',
            'alfa',
            'sakit',
            'lembur',
            'cuti',
            'bulan',
            'alfaDates',
            'jumlahAlfaSeluruhUser'
        ));
    }
}
