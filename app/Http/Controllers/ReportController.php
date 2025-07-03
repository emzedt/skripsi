<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiSales;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use App\Exports\AbsensiSalesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Show report filter page for regular attendance
    public function absensiReport()
    {
        return view('reports.absensi');
    }

    // Show report filter page for sales attendance
    public function absensiSalesReport()
    {
        return view('reports.absensi_sales');
    }

    // Export regular attendance to Excel
    public function exportAbsensiExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new AbsensiExport($startDate, $endDate), 'absensi-' . now()->format('Y-m-d') . '.xlsx');
    }

    // Export regular attendance to PDF
    public function exportAbsensiPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $absensi = Absensi::with('user')
            ->when($startDate, fn($query) => $query->whereDate('tanggal', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tanggal', '<=', $endDate))
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.absensi', compact('absensi', 'startDate', 'endDate'));
        return $pdf->download('absensi-' . now()->format('Y-m-d') . '.pdf');
    }

    // Export sales attendance to Excel
    public function exportAbsensiSalesExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new AbsensiSalesExport($startDate, $endDate), 'absensi-sales-' . now()->format('Y-m-d') . '.xlsx');
    }

    // Export sales attendance to PDF
    public function exportAbsensiSalesPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $absensiSales = AbsensiSales::with('user')
            ->when($startDate, fn($query) => $query->whereDate('tanggal', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('tanggal', '<=', $endDate))
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.absensi_sales', compact('absensiSales', 'startDate', 'endDate'));
        return $pdf->download('absensi-sales-' . now()->format('Y-m-d') . '.pdf');
    }
}
