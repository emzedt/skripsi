<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiSalesController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaceRegistrationController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\HakCutiController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PeopleDevelopmentController;
use App\Http\Controllers\PermintaanLemburController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SakitController;
use App\Http\Controllers\StatusKaryawanController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/karyawan', [UserController::class, 'index'])->name('karyawan.index')->middleware('permission:Lihat Karyawan');
    Route::group(['middleware' => 'permission:Lihat Karyawan'], function () {
        Route::get('/karyawan/create', [UserController::class, 'create'])->name('karyawan.create');
        Route::post('/karyawan', [UserController::class, 'store'])->name('karyawan.store');
    });
    Route::group(['middleware' => 'permission:Edit Karyawan'], function () {
        Route::get('karyawan/{user}/edit', [UserController::class, 'edit'])->name('karyawan.edit');
        Route::put('/karyawan/{user}', [UserController::class, 'update'])->name('karyawan.update');
    });
    Route::delete('karyawan/{user}', [UserController::class, 'destroy'])->name('karyawan.destroy')->middleware('permission:Hapus Karyawan');

    // Jabatan Routes
    Route::middleware(['permission:Lihat Jabatan'])->group(function () {
        Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    });

    Route::middleware(['permission:Tambah Jabatan'])->group(function () {
        Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
        Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    });

    Route::middleware(['permission:Edit Jabatan'])->group(function () {
        Route::get('jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
        Route::put('jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
    });

    Route::middleware(['permission:Hapus Jabatan'])->group(function () {
        Route::delete('jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
    });

    Route::middleware(['permission:Kelola Hirarki Jabatan'])->group(function () {
        Route::prefix('api')->group(function () {
            Route::get('/jabatan/{jabatan}/hierarchy', [JabatanController::class, 'getHierarchy']);
            Route::post('/jabatan/{jabatan}/hierarchy', [JabatanController::class, 'saveHierarchy']);
        });
    });

    // Hak Akses Routes
    Route::prefix('hak-akses')->middleware(['permission:Lihat Hak Akses'])->group(function () {
        Route::get('/', [HakAksesController::class, 'index'])->name('hak-akses.index');
        Route::middleware(['permission:Edit Hak Akses'])->group(function () {
            Route::get('/{jabatan}/edit', [HakAksesController::class, 'edit'])->name('hak-akses.edit');
            Route::put('/{jabatan}', [HakAksesController::class, 'update'])->name('hak-akses.update');
        });
    });

    // Status Karyawan Routes
    Route::middleware(['permission:Lihat Status Karyawan'])->group(function () {
        Route::get('/status-karyawan', [StatusKaryawanController::class, 'index'])->name('status_karyawan.index');
    });

    Route::middleware(['permission:Tambah Status Karyawan'])->group(function () {
        Route::get('/status-karyawan/create', [StatusKaryawanController::class, 'create'])->name('status_karyawan.create');
        Route::post('/status-karyawan', [StatusKaryawanController::class, 'store'])->name('status_karyawan.store');
    });

    Route::middleware(['permission:Edit Status Karyawan'])->group(function () {
        Route::get('status-karyawan/{statusKaryawan}/edit', [StatusKaryawanController::class, 'edit'])->name('status_karyawan.edit');
        Route::put('status-karyawan/{statusKaryawan}', [StatusKaryawanController::class, 'update'])->name('status_karyawan.update');
    });

    Route::middleware(['permission:Hapus Status Karyawan'])->group(function () {
        Route::delete('status-karyawan/{statusKaryawan}', [StatusKaryawanController::class, 'destroy'])->name('status_karyawan.destroy');
    });

    // Lokasi Routes
    Route::middleware(['permission:Lihat Lokasi'])->group(function () {
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    });

    Route::middleware(['permission:Tambah Lokasi'])->group(function () {
        Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    });

    Route::middleware(['permission:Edit Lokasi'])->group(function () {
        Route::get('lokasi/{lokasi}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
        Route::put('lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
    });

    Route::middleware(['permission:Hapus Lokasi'])->group(function () {
        Route::delete('lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
    });

    // Hak Cuti Routes
    Route::middleware(['permission:Lihat Hak Cuti'])->group(function () {
        Route::get('/reset-cuti', [HakCutiController::class, 'index'])->name('hak_cuti.index');
    });

    Route::middleware(['permission:Kelola Hak Cuti'])->group(function () {
        Route::post('/reset-cuti', [HakCutiController::class, 'store'])->name('hak_cuti.store');
        Route::put('/reset-cuti', [HakCutiController::class, 'update'])->name('hak_cuti.update');
    });

    // Face Registration Routes
    Route::prefix('face-registration')->middleware(['permission:Kelola Registrasi Wajah'])->group(function () {
        Route::get('/{id}', [FaceRegistrationController::class, 'index'])->name('face.registration');
        Route::post('/save', [FaceRegistrationController::class, 'save'])->name('face.save');
        Route::post('/ajaxDescrip', [FaceRegistrationController::class, 'ajaxDescrip'])->name('face.ajaxDescrip');
    });

    // Absensi Routes
    Route::middleware(['permission:Lihat Absensi'])->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');
    });

    Route::middleware(['permission:Tambah Absensi'])->group(function () {
        Route::get('/absensi-masuk', [AbsensiController::class, 'absensiMasuk'])->name('absensi.absensi-masuk');
        Route::post('/absensi-masuk', [AbsensiController::class, 'storeMasuk'])->name('absensi-masuk.store');
        Route::get('/absensi-keluar', [AbsensiController::class, 'absensiKeluar'])->name('absensi.absensi-keluar');
        Route::post('/absensi-keluar', [AbsensiController::class, 'storeKeluar'])->name('absensi-keluar.store');
    });

    Route::middleware(['permission:Edit Absensi'])->group(function () {
        Route::get('absensi/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi/{absensi}', [AbsensiController::class, 'update'])->name('absensi.update');
    });

    Route::middleware(['permission:Hapus Absensi'])->group(function () {
        Route::delete('absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    });

    // Location Validation (tanpa permission khusus)
    Route::post('/validate-location', [AbsensiController::class, 'validateLocation'])->name('validate-location');
    Route::get('/ajaxGetNeural', [AbsensiController::class, 'ajaxGetNeural']);

    // Absensi Sales Routes
    Route::middleware(['permission:Lihat Absensi Sales'])->group(function () {
        Route::get('/absensi-sales', [AbsensiSalesController::class, 'index'])->name('absensi_sales.index');
    });

    Route::middleware(['permission:Tambah Absensi Sales'])->group(function () {
        Route::get('/absensi-sales/create', [AbsensiSalesController::class, 'create'])->name('absensi_sales.create');
        Route::post('/absensi-sales', [AbsensiSalesController::class, 'store'])->name('absensi_sales.store');
    });

    Route::middleware(['permission:Lihat Detail Absensi Sales'])->group(function () {
        Route::get('/absensi-sales/{absensiSales}', [AbsensiSalesController::class, 'show'])->name('absensi_sales.show');
    });

    Route::middleware(['permission:Edit Absensi Sales'])->group(function () {
        Route::get('absensi-sales/{absensiSales}/edit', [AbsensiSalesController::class, 'edit'])->name('absensi_sales.edit');
        Route::put('absensi-sales/{absensiSales}', [AbsensiSalesController::class, 'update'])->name('absensi_sales.update');
    });

    Route::middleware(['permission:Hapus Absensi Sales'])->group(function () {
        Route::delete('absensi-sales/{absensiSales}', [AbsensiSalesController::class, 'destroy'])->name('absensi_sales.destroy');
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/absensi', [ReportController::class, 'absensiReport'])->name('reports.absensi');
        Route::get('/absensi-sales', [ReportController::class, 'absensiSalesReport'])->name('reports.absensi_sales');

        Route::get('/absensi/export-excel', [ReportController::class, 'exportAbsensiExcel'])->name('reports.absensi.excel');
        Route::get('/absensi/export-pdf', [ReportController::class, 'exportAbsensiPdf'])->name('reports.absensi.pdf');

        Route::get('/absensi-sales/export-excel', [ReportController::class, 'exportAbsensiSalesExcel'])->name('reports.absensi_sales.excel');
        Route::get('/absensi-sales/export-pdf', [ReportController::class, 'exportAbsensiSalesPdf'])->name('reports.absensi_sales.pdf');
    });

    // Cuti Routes
    Route::middleware(['permission:Lihat Pengajuan Cuti'])->group(function () {
        Route::get('/pengajuan-cuti', [CutiController::class, 'index'])->name('pengajuan_cuti.index');
    });

    Route::middleware(['permission:Tambah Pengajuan Cuti'])->group(function () {
        Route::get('/pengajuan-cuti/create', [CutiController::class, 'create'])->name('pengajuan_cuti.create');
        Route::post('/pengajuan-cuti', [CutiController::class, 'store'])->name('pengajuan_cuti.store');
    });

    Route::middleware(['permission:Edit Pengajuan Cuti'])->group(function () {
        Route::get('pengajuan-cuti/{cuti}/edit', [CutiController::class, 'edit'])->name('pengajuan_cuti.edit');
        Route::put('pengajuan-cuti/{cuti}', [CutiController::class, 'update'])->name('pengajuan_cuti.update');
    });

    Route::middleware(['permission:Hapus Pengajuan Cuti'])->group(function () {
        Route::delete('pengajuan-cuti/{cuti}', [CutiController::class, 'destroy'])->name('pengajuan_cuti.destroy');
    });

    // Persetujuan Cuti Routes
    Route::middleware(['permission:Lihat Persetujuan Cuti'])->group(function () {
        Route::get('/persetujuan-cuti', [CutiController::class, 'indexPersertujuanCuti'])->name('persetujuan_cuti.index');
    });

    Route::middleware(['permission:Proses Persetujuan Cuti'])->group(function () {
        Route::get('persetujuan-cuti/{cuti}/edit', [CutiController::class, 'editPersetujuanCuti'])->name('persetujuan_cuti.edit');
        Route::put('persetujuan-cuti/{cuti}', [CutiController::class, 'updatePersetujuanCuti'])->name('persetujuan_cuti.update');
    });

    Route::prefix('sakit')->group(function () {
        Route::middleware(['permission:Lihat Pengajuan Sakit'])->group(function () {
            Route::get('/', [SakitController::class, 'index'])->name('sakit.index');
        });
        Route::middleware(['permission:Tambah Pengajuan Sakit'])->group(function () {
            Route::get('/create', [SakitController::class, 'create'])->name('sakit.create');
            Route::post('/', [SakitController::class, 'store'])->name('sakit.store');
        });
        Route::middleware(['permission:Edit Pengajuan Sakit'])->group(function () {
            Route::get('/{sakit}/edit', [SakitController::class, 'edit'])->name('sakit.edit');
            Route::put('/{sakit}', [SakitController::class, 'update'])->name('sakit.update');
        });
        Route::middleware(['permission:Hapus Pengajuan Sakit'])->group(function () {
            Route::delete('/{sakit}', [SakitController::class, 'destroy'])->name('sakit.destroy');
        });
    });

    Route::prefix('izin')->group(function () {
        Route::middleware(['permission:Lihat Pengajuan Izin'])->group(function () {
            Route::get('/', [IzinController::class, 'index'])->name('izin.index');
        });
        Route::middleware(['permission:Tambah Pengajuan Izin'])->group(function () {
            Route::get('/create', [IzinController::class, 'create'])->name('izin.create');
            Route::post('/', [IzinController::class, 'store'])->name('izin.store');
        });
        Route::middleware(['permission:Edit Pengajuan Izin'])->group(function () {
            Route::get('/{izin}/edit', [IzinController::class, 'edit'])->name('izin.edit');
            Route::put('/{izin}', [IzinController::class, 'update'])->name('izin.update');
        });
        Route::middleware(['permission:Hapus Pengajuan Izin'])->group(function () {
            Route::delete('/{izin}', [IzinController::class, 'destroy'])->name('izin.destroy');
        });
    });

    // Gaji Routes
    Route::prefix('gaji')->middleware(['permission:Lihat Gaji'])->group(function () {
        Route::get('/', [GajiController::class, 'index'])->name('gaji.index');
        Route::middleware(['permission:Tambah Gaji'])->group(function () {
            Route::get('/create', [GajiController::class, 'create'])->name('gaji.create');
            Route::post('/', [GajiController::class, 'store'])->name('gaji.store');
        });
        Route::middleware(['permission:Edit Gaji'])->group(function () {
            Route::get('/{id}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
            Route::put('/{id}', [GajiController::class, 'update'])->name('gaji.update');
        });
        Route::middleware(['permission:Hapus Gaji'])->group(function () {
            Route::delete('/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');
        });
    });

    // Penggajian Routes
    Route::prefix('penggajian')->middleware(['permission:Lihat Penggajian'])->group(function () {
        Route::get('/', [PenggajianController::class, 'index'])->name('penggajian.index');
        Route::middleware(['permission:Tambah Penggajian'])->group(function () {
            Route::get('/create', [PenggajianController::class, 'create'])->name('penggajian.create');
            Route::post('/store', [PenggajianController::class, 'store'])->name('penggajian.store');
        });
        Route::get('/{penggajian}', [PenggajianController::class, 'show'])->name('penggajian.show');
        Route::get('/{penggajian}/slip', [PenggajianController::class, 'slip'])->name('penggajian.slip');
        Route::middleware(['permission:Edit Penggajian'])->group(function () {
            Route::get('/{penggajian}/edit', [PenggajianController::class, 'edit'])->name('penggajian.edit');
            Route::put('/{penggajian}/update', [PenggajianController::class, 'update'])->name('penggajian.update');
        });
        Route::middleware(['permission:Hapus Penggajian'])->group(function () {
            Route::delete('/{penggajian}/destroy', [PenggajianController::class, 'destroy'])->name('penggajian.destroy');
        });
    });

    // Lembur Routes
    Route::prefix('permintaan-lembur')->middleware(['permission:Lihat Permintaan Lembur'])->group(function () {
        Route::get('/', [PermintaanLemburController::class, 'index'])->name('permintaan_lembur.index');
        Route::middleware(['permission:Tambah Permintaan Lembur'])->group(function () {
            Route::get('/create', [PermintaanLemburController::class, 'create'])->name('permintaan_lembur.create');
            Route::post('/', [PermintaanLemburController::class, 'store'])->name('permintaan_lembur.store');
        });
        Route::middleware(['permission:Edit Permintaan Lembur'])->group(function () {
            Route::get('/{permintaanLembur}/edit', [PermintaanLemburController::class, 'edit'])->name('permintaan_lembur.edit');
            Route::put('/{permintaanLembur}', [PermintaanLemburController::class, 'update'])->name('permintaan_lembur.update');
        });
        Route::middleware(['permission:Hapus Permintaan Lembur'])->group(function () {
            Route::delete('/{permintaanLembur}', [PermintaanLemburController::class, 'destroy'])->name('permintaan_lembur.destroy');
        });
        Route::get('/show/{permintaanLembur}', [PermintaanLemburController::class, 'show'])->name('permintaan_lembur.show');
    });

    // People Development Routes
    Route::prefix('people-development')->middleware(['permission:Lihat People Development'])->group(function () {
        Route::get('/data', [PeopleDevelopmentController::class, 'data'])->name('people_development.data');
        Route::get('/', [PeopleDevelopmentController::class, 'index'])->name('people_development.index');
        Route::get('/{user}', [PeopleDevelopmentController::class, 'show'])->name('people_development.show');
        Route::middleware(['permission:Tambah People Development'])->group(function () {
            Route::get('/{user}/create', [PeopleDevelopmentController::class, 'create'])->name('people_development.create');
            Route::post('/', [PeopleDevelopmentController::class, 'store'])->name('people_development.store');
        });
        Route::middleware(['permission:Edit People Development'])->group(function () {
            Route::get('/{user}/edit/{development}', [PeopleDevelopmentController::class, 'edit'])->name('people_development.edit');
            Route::put('/{user}/{development}', [PeopleDevelopmentController::class, 'update'])->name('people_development.update');
        });
        Route::middleware(['permission:Hapus People Development'])->group(function () {
            Route::delete('/{user}/{development}', [PeopleDevelopmentController::class, 'destroy'])->name('people_development.destroy');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['permission:Lihat Kalender'])->group(function () {
        Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');
        Route::get('/hari-libur', [KalenderController::class, 'getHariLibur']);
    });

    Route::middleware(['permission:Kelola Hari Libur'])->group(function () {
        Route::post('/hari-libur', [KalenderController::class, 'store']);
        Route::put('/hari-libur/{id}', [KalenderController::class, 'update']);
        Route::delete('/hari-libur/{id}', [KalenderController::class, 'destroy']);
    });

    Route::middleware(['permission:Kelola Sampah'])->group(function () {
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
        Route::get('/trash/data', [TrashController::class, 'getData'])->name('trash.data');
        Route::get('/trash/{model}/{id}', [TrashController::class, 'show'])->name('trash.show');
        Route::post('/trash/{model}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    });
});

require __DIR__ . '/auth.php';
