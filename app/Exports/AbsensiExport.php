<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Absensi::with('user')
            ->when($this->startDate, fn($query) => $query->whereDate('tanggal', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('tanggal', '<=', $this->endDate))
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Lokasi Masuk',
            'Lokasi Keluar'
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->user->nama,
            $absensi->tanggal,
            $absensi->jam_masuk,
            $absensi->jam_keluar,
            $absensi->status,
            $absensi->latitude_masuk && $absensi->longitude_masuk
                ? "https://www.google.com/maps?q={$absensi->latitude_masuk},{$absensi->longitude_masuk}"
                : '-',
            $absensi->latitude_keluar && $absensi->longitude_keluar
                ? "https://www.google.com/maps?q={$absensi->latitude_keluar},{$absensi->longitude_keluar}"
                : '-',
        ];
    }
}
