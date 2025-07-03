<?php

namespace App\Exports;

use App\Models\AbsensiSales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiSalesExport implements FromCollection, WithHeadings, WithMapping
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
        return AbsensiSales::with('user')
            ->when($this->startDate, fn($query) => $query->whereDate('tanggal', '>=', $this->startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('tanggal', '<=', $this->endDate))
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Sales',
            'Tanggal',
            'Jam',
            'Status',
            'Deskripsi',
            'Foto'
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->user->nama,
            $absensi->tanggal->format('d/m/Y'),
            $absensi->created_at->format('H:i'),
            $absensi->status,
            $absensi->deskripsi,
            $absensi->foto ? asset('storage/absensi_sales/' . $absensi->foto) : 'Tidak ada foto'
        ];
    }
}
