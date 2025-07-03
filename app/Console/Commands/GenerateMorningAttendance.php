<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\AbsensiSales;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Sakit;
use App\Models\Kalender;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMorningAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-morning-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate morning attendance records for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        // Check if today is a holiday
        $isHoliday = Kalender::whereDate('tanggal', $today)->first();

        if ($isHoliday) {
            if ($isHoliday->jenis_libur === 'Cuti Bersama') {
                // Update all users' leave balance
                User::query()->each(function ($user) {
                    // Prioritize deducting from sisa_hak_cuti_bersama first
                    if ($user->sisa_hak_cuti_bersama > 0) {
                        $user->decrement('sisa_hak_cuti_bersama');
                    } else {
                        // Only deduct from sisa_hak_cuti if sisa_hak_cuti_bersama is 0
                        $user->decrement('sisa_hak_cuti');
                    }
                    // Ensure values don't go below 0
                    $user->update([
                        'sisa_hak_cuti' => max(0, $user->sisa_hak_cuti),
                        'sisa_hak_cuti_bersama' => max(0, $user->sisa_hak_cuti_bersama)
                    ]);
                });

                $this->info('Today is Cuti Bersama. Deducted leave balance from all users.');
            } else {
                $this->info('Today is a regular holiday (Libur), no leave balance deduction');
            }
            return;
        }

        // Process regular employees (with face recognition)
        $regularUsers = User::whereNotNull('foto_face_recognition')->get();
        $this->processUsers($regularUsers, $today, 'regular');

        $this->info("Morning attendance generated for " . $today);
    }

    protected function processUsers($users, $today, $type)
    {
        foreach ($users as $user) {
            $status = $this->determineAttendanceStatus($user, $today);

            if ($type === 'regular') {
                Absensi::updateOrCreate(
                    ['user_id' => $user->id, 'tanggal' => $today],
                    ['status' => $status]
                );
            }
        }
    }

    protected function determineAttendanceStatus($user, $today)
    {
        // Check in priority order: Sakit > Izin > Cuti
        if ($this->hasApprovedSakit($user, $today)) {
            return 'Sakit';
        }

        if ($this->hasApprovedIzin($user, $today)) {
            return 'Izin';
        }

        if ($this->hasApprovedCuti($user, $today)) {
            return 'Cuti';
        }

        return 'Belum Absen';
    }

    protected function hasApprovedSakit($user, $date)
    {
        return Sakit::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->exists();
    }

    protected function hasApprovedIzin($user, $date)
    {
        return Izin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->where('jenis_izin', 'Satu Hari')
            ->whereDate('tanggal', $date)
            ->exists();
    }

    protected function hasApprovedCuti($user, $date)
    {
        return Cuti::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai_cuti', '<=', $date)
            ->whereDate('tanggal_selesai_cuti', '>=', $date)
            ->exists();
    }
}
