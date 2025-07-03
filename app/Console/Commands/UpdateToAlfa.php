<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\AbsensiSales;
use App\Models\Kalender;
use Illuminate\Console\Command;

class UpdateToAlfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-to-alfa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        // Cek apakah hari ini libur
        $isHoliday = Kalender::whereDate('tanggal', $today)
            ->exists();

        // Jika hari libur, skip proses
        if ($isHoliday) {
            $this->info('Hari ini libur, absensi tidak digenerate');
            return;
        }

        $updated = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Belum Absen')
            ->update(['status' => 'Alfa']);

        // $salesUpdated = AbsensiSales::whereDate('tanggal', $today)
        //     ->where('status_absensi_sales', 'Belum Absen')
        //     ->update(['status_absensi_sales' => 'Alfa']);

        $this->info("Updated {$updated} from Absensi records to Alfa status");
        // $this->info("Updated {$salesUpdated} from Absensi Sales records to Alfa status");
    }
}
