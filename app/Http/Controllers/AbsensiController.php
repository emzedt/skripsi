<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsensiRequest;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Kalender;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            if ($user->isAdmin() || $user->isHRD()) {
                $data = Absensi::with('user')->select('absensis.*');
            } else {
                $data = Absensi::with('user')->select('absensis.*')->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);

                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $query->orWhereIn(
                            'user_id',
                            $user->subordinates()->select('users.id')
                        );
                    }
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user.nama', function ($row) {
                    return $row->user ? $row->user->nama : 'N/A';
                })
                ->addColumn('tanggal', function ($row) {
                    return \Carbon\Carbon::parse($row->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
                })
                ->addColumn('tanggal_formatted', function ($row) {
                    return $row->tanggal->format('d-m-Y');
                })
                ->addColumn('jam_masuk', function ($row) {
                    return $row->jam_masuk ?: '-';
                })
                ->addColumn('jam_keluar', function ($row) {
                    return $row->jam_keluar ?: '-';
                })
                ->addColumn('aksi', function ($row) {
                    return $row->id;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        // ... (kode Anda untuk menampilkan view)
        $lokasis = Lokasi::all();
        return view('absensi.index', compact('lokasis'));
    }

    public function show(Absensi $absensi)
    {
        $absensis = Absensi::with(['user:id,nama', 'lokasi:id,nama'])
            ->select(
                'id',
                'tanggal',
                'foto_masuk',
                'jam_masuk',
                'latitude_masuk',
                'longitude_masuk',
                'foto_keluar',
                'jam_keluar',
                'latitude_keluar',
                'longitude_keluar',
                'status',
                'user_id',
                'lokasi_id'
            )
            ->get();

        return view('absensi.show', compact('absensis', 'absensi'));
    }

    public function absensiMasuk()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        if (empty($user->foto_face_recognition)) {
            return redirect()->route('face.registration', ['id' => $user->id]);
        }

        // Ambil izin disetujui untuk hari ini
        $izinHariIni = Izin::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('status', 'Disetujui')
            ->whereIn('jenis_izin', ['Satu Hari', 'Setengah Hari Pagi', 'Setengah Hari Siang'])
            ->first();

        if ($izinHariIni) {
            if ($izinHariIni->jenis_izin === 'Setengah Hari Pagi' && $now->lt($today->copy()->addHours(12))) {
                return redirect()->route('absensi.index')->with('error', 'Anda hanya bisa absen masuk setelah pukul 12:00 karena izin Setengah Hari Pagi.');
            }

            if ($izinHariIni->jenis_izin === 'Setengah Hari Siang' && $now->gte($today->copy()->addHours(12))) {
                return redirect()->route('absensi.index')->with('error', 'Anda hanya bisa absen masuk sebelum pukul 12:00 karena izin Setengah Hari Siang.');
            }

            if ($izinHariIni->jenis_izin === 'Satu Hari') {
                return redirect()->route('absensi.index')->with('error', 'Anda izin Satu Hari. Tidak perlu absensi untuk hari ini');
            }
        }

        $todayIs = now()->toDateString();
        $isHoliday = Kalender::whereDate('tanggal', $todayIs)->first();
        if ($isHoliday && ($isHoliday->jenis_libur === 'Cuti Bersama' || $isHoliday->jenis_libur === 'Libur')) {
            return redirect()->route('absensi.index')->with('error', 'Anda tidak bisa absen karena lagi libur atau cuti bersama!');
        }


        $belumAbsen = Absensi::where('user_id', $user->id)
            ->where('status', 'Belum Absen')
            ->count();

        if ($belumAbsen > 0) {
            return view('absensi.absensi-masuk');
        }

        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAbsensi) {
            return redirect()->route('absensi.index')->with('error', 'Anda sudah absen masuk hari ini!');
        }

        return view('absensi.absensi-masuk');
    }

    public function absensiKeluar()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if (empty($user->foto_face_recognition)) {
            return redirect()->route('face.registration', ['id' => $user->id]);
        }

        // Check if already checked out today
        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereNotNull('jam_keluar')
            ->first();

        if ($existingAbsensi) {
            return redirect()->route('absensi.index')->with('error', 'Anda sudah absen keluar hari ini!');
        }

        // Check if user has checked in today
        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereNull('jam_keluar')
            ->first();

        if (!$absensi) {
            return redirect()->route('absensi.index')->with('error', 'Anda belum absensi masuk hari ini!');
        }

        return view('absensi.absensi-keluar');
    }

    public function storeMasuk(AbsensiRequest $request)
    {
        try {
            // Validate the request first
            $validated = $request->validated();

            // Find nearest location and validate
            $nearestLocationId = $this->findNearestLocation($request->latitude, $request->longitude);

            if (!$nearestLocationId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda berada di luar radius lokasi terdekat'
                ], 400);
            }

            // Process image
            $imgData = $request->foto_masuk;
            $fileName = 'absensi_masuk_' . time() . '.jpg';
            $relativePath = 'absensi_masuk/' . $fileName;
            $fullPath = storage_path('app/public/' . $relativePath);

            // Save image
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
            file_put_contents($fullPath, $image);

            // Determine status based on time
            $currentTime = now();
            $jamMasuk = $currentTime->toTimeString();
            $status = 'Hadir'; // Default status

            // Cek apakah user memiliki izin 'Setengah Hari Siang' hari ini
            $izinSiang = Izin::where('user_id', Auth::id())
                ->where('status', 'Disetujui')
                ->where('jenis_izin', 'Setengah Hari Siang')
                ->whereDate('tanggal', $currentTime->toDateString())
                ->exists();

            $izinPagi = Izin::where('user_id', Auth::id())
                ->where('status', 'Disetujui')
                ->where('jenis_izin', 'Setengah Hari Pagi')
                ->whereDate('tanggal', $currentTime->toDateString())
                ->exists();

            // Penentuan status telat
            if ($izinSiang) {
                // Izin siang: jika masuk setelah jam 13:00 maka telat
                if ($currentTime->format('H:i:s') > '13:00:00') {
                    $status = 'Telat';
                }
            } else if ($izinPagi) {
                if ($currentTime->format('H:i:s') > '08:00:00') {
                    $status = 'Telat';
                }
            } else {
                // Biasa: jika masuk setelah jam 08:00 maka telat
                if ($currentTime->format('H:i:s') > '08:00:00') {
                    $status = 'Telat';
                }
            }

            // Find existing attendance record for today
            $absensi = Absensi::where('user_id', Auth::id())
                ->whereDate('tanggal', now()->toDateString())
                ->where('status', 'Belum Absen')
                ->first();

            if ($absensi) {
                // Update existing record
                $absensi->update([
                    'foto_masuk' => $relativePath,
                    'jam_masuk' => $jamMasuk,
                    'latitude_masuk' => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'lokasi_id' => $nearestLocationId,
                    'status' => $status
                ]);
            } else {
                // Create new record if not exists
                $absensi = Absensi::create([
                    'user_id' => Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'foto_masuk' => $relativePath,
                    'jam_masuk' => $jamMasuk,
                    'latitude_masuk' => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'lokasi_id' => $nearestLocationId,
                    'status' => $status,
                ]);
            }

            return response()->json([
                'status' => 'masuk',
                'message' => 'Berhasil Absen Masuk',
                'data' => $absensi
            ]);
        } catch (\Exception $e) {
            // Delete file if error occurred
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeKeluar(AbsensiRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $today = now()->toDateString();

        // Rest of your existing code...
        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereNotNull('jam_keluar')
            ->first();

        if ($existingAbsensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah absen pulang hari ini!'
            ], 400);
        }

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereNull('jam_keluar')
            ->first();

        if (!$absensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum absen masuk hari ini!'
            ], 400);
        }

        // Process image
        $imgData = $request->foto_keluar;
        $fileName = 'absensi_keluar_' . time() . '.jpg';
        $relativePath = 'absensi_keluar/' . $fileName;
        $fullPath = storage_path('app/public/' . $relativePath);

        // Save image
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));
        file_put_contents($fullPath, $image);

        $absensi->update([
            'foto_keluar' => $relativePath,
            'jam_keluar' => now()->toTimeString(),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Absen Keluar',
            'data' => $absensi
        ]);
    }

    private function findNearestLocation($lat, $lon)
    {
        $lokasis = Lokasi::all();
        $terdekat = null;
        $minDistance = 999999;

        foreach ($lokasis as $lokasi) {
            $distance = $this->haversine($lat, $lon, $lokasi->latitude, $lokasi->longitude);
            if ($distance <= $lokasi->radius && $distance < $minDistance) {
                $terdekat = $lokasi->id;
                $minDistance = $distance;
            }
        }

        return $terdekat;
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371e3; // meters
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $deltaPhi = deg2rad($lat2 - $lat1);
        $deltaLambda = deg2rad($lon2 - $lon1);

        $a = sin($deltaPhi / 2) * sin($deltaPhi / 2) +
            cos($phi1) * cos($phi2) *
            sin($deltaLambda / 2) * sin($deltaLambda / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c; // in meters
    }

    public function ajaxGetNeural()
    {
        try {
            // Use absolute path with Laravel's helper
            $filePath = public_path('neural.json');

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Neural data file not found'], 404);
            }

            // Get the raw content
            $fileContents = file_get_contents($filePath);

            // Remove UTF-8 BOM if present
            $bom = pack('H*', 'EFBBBF');
            if (strncmp($fileContents, $bom, 3) === 0) {
                $fileContents = substr($fileContents, 3);
            }

            // Parse JSON
            $data = json_decode($fileContents);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid JSON: ' . json_last_error_msg()
                ], 500);
            }

            // Return as proper Laravel JSON response
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // public function ajaxGetNeural()
    // {
    //     $inp = file_get_contents('neural.json');
    //     $tempArray = json_decode($inp);
    //     $jsonData = json_encode($tempArray);
    //     echo $jsonData;
    // }

    public function validateLocation(Request $request)
    {
        $validate = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'lokasi_id' => 'nullable|exists:lokasis,id'
        ]);

        if ($validate['lokasi_id']) {
            // For check-out - validate against specific location
            $lokasi = Lokasi::find($validate['lokasi_id']);
            $distance = $this->haversine(
                $validate['latitude'],
                $validate['longitude'],
                $lokasi->latitude,
                $lokasi->longitude
            );

            return response()->json([
                'valid' => $distance <= $lokasi->radius,
                'message' => $distance <= $lokasi->radius
                    ? 'Lokasi valid'
                    : 'Anda berada di luar radius lokasi kantor'
            ]);
        } else {
            // For check-in - find any valid location
            $nearestLocationId = $this->findNearestLocation($validate['latitude'], $validate['longitude']);
            return response()->json([
                'valid' => $nearestLocationId !== null,
                'message' => $nearestLocationId !== null
                    ? 'Lokasi valid'
                    : 'Tidak ada lokasi kantor dalam radius yang valid'
            ]);
        }
    }

    public function edit(Absensi $absensi)
    {
        $lokasis = Lokasi::all();

        return view('absensi.edit', compact('absensi', 'lokasis'));
    }

    /**
     * Update the specified absensi in storage.
     */
    public function update(Request $request, Absensi $absensi)
    {
        // Validate the request data
        $validated = $request->validate([
            'foto_masuk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'jam_masuk' => 'nullable|date_format:H:i:s',
            'latitude_masuk' => 'nullable|numeric|between:-90,90',
            'longitude_masuk' => 'nullable|numeric|between:-180,180',
            'foto_keluar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'jam_keluar' => 'nullable|date_format:H:i:s|after_or_equal:jam_masuk',
            'latitude_keluar' => 'nullable|numeric|between:-90,90',
            'longitude_keluar' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:Hadir,Alfa,Izin,Cuti,Telat,Belum Absen',
            'lokasi_id' => 'nullable|exists:lokasis,id',
        ]);

        if ($request->hasFile('foto_masuk')) {
            if ($absensi->foto_masuk && Storage::disk('public')->exists($absensi->foto_masuk)) {
                Storage::disk('public')->delete($absensi->foto_masuk);
            }

            $file = $request->file('foto_masuk');
            $fileName = 'absensi_masuk_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'absensi_masuk/' . $fileName;
            Storage::disk('public')->put('absensi_masuk/' . $fileName, file_get_contents($file));
            $validated['foto_masuk'] = $path;
        } else {
            unset($validated['foto_masuk']);
        }

        if ($request->hasFile('foto_keluar')) {
            // Delete old foto if exists
            if ($absensi->foto_keluar && Storage::disk('public')->exists($absensi->foto_keluar)) {
                Storage::disk('public')->delete($absensi->foto_keluar);
            }

            $file = $request->file('foto_keluar');
            $fileName = 'absensi_keluar_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'absensi_keluar/' . $fileName;
            Storage::disk('public')->put('absensi_keluar/' . $fileName, file_get_contents($file));
            $validated['foto_keluar'] = $path;
        } else {
            unset($validated['foto_keluar']);
        }

        // Update the absensi record
        $absensi->update($validated);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil diperbarui');
    }

    public function destroy(Absensi $absensi)
    {
        try {
            // Hapus karyawan
            $absensi->delete();

            $namaKaryawan = $absensi->user->nama;
            $tanggalAbsensi = Carbon::parse($absensi->tanggal)->format('d-m-Y');

            // Return JSON sukses
            return response()->json([
                'success' => true,
                'message' => "Absensi $namaKaryawan pada tanggal $tanggalAbsensi berhasil dihapus."
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error jika ada data yang terkait (foreign key constraint)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => "Absensi $namaKaryawan tidak dapat dihapus karena masih memiliki data yang terkait."
                ], 400);
            }

            // Tangani error lain
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan saat menghapus absensi $namaKaryawan. Silakan coba lagi."
            ], 500);
        }
    }
}
