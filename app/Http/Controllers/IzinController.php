<?php
// app/Http/Controllers/IzinController.php
namespace App\Http\Controllers;

use App\Mail\PermohonanDiajukanEmail;
use App\Mail\PermohonanDiketahuiEmail;
use App\Mail\PermohonanDiterimaEmail;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                $query = Izin::with('user:id,nama');
            } else {
                $query = Izin::with('user:id,nama')->where(function ($q) use ($user) {
                    // Tampilkan data milik user itu sendiri
                    $q->where('user_id', $user->id);

                    // Jika user adalah seorang manajer, tampilkan juga data bawahannya
                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $subordinateIds = $user->subordinates()->pluck('users.id');
                        $q->orWhereIn('user_id', $subordinateIds);
                    }
                });
            }

            return DataTables::of($query)
                ->editColumn('tanggal', fn($izin) => Carbon::parse($izin->tanggal)->format('d M Y'))
                ->addColumn('aksi', function ($izin) {
                    $editUrl = route('izin.edit', $izin->id);
                    $deleteFunc = "confirmDelete({$izin->id}, '{$izin->user->nama}')";

                    $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                    $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';

                    $actions = '<div class="flex space-x-2 justify-center">';
                    $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Ubah">' . $editIcon . '</a>';
                    if ($izin->status == 'Menunggu') {
                        $actions .= '<button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('izin.index');
    }

    public function create()
    {
        return view('izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_izin' => 'required|in:Satu Hari,Setengah Hari Pagi,Setengah Hari Siang',
            'alasan' => 'required|string',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $dokumenPendukung = $request->file('dokumen_pendukung');
        $namaFile = null;
        $path = null;

        if ($dokumenPendukung) {
            $namaFile = now()->format('YmdHis') . '.' . $dokumenPendukung->getClientOriginalExtension();
            $path = 'izin/' . $namaFile;
            Storage::disk('public')->put($path, file_get_contents($dokumenPendukung));
        }

        Izin::create([
            'tanggal' => $request->tanggal,
            'jenis_izin' => $request->jenis_izin,
            'alasan' => $request->alasan,
            'dokumen_pendukung' => $path,
            'user_id' => Auth::id(),
        ]);

        $user = Auth::user();
        $boss = $user->boss();

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiajukanEmail(
                $boss->nama,
                $user->nama,
                'Izin ' . $request->jenis_izin,
                $request->tanggal,
                $request->tanggal
            ));
        } else {
            // fallback ke HRD kalau tidak punya boss
            Mail::to($user->email)->send(new PermohonanDiajukanEmail(
                $user->nama,
                $user->nama,
                'Izin ' . $request->jenis_izin,
                $request->tanggal,
                $request->tanggal
            ));
        }

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dikirim');
    }

    public function show(Izin $izin)
    {
        return view('izin.show', compact('izin'));
    }

    public function edit(Izin $izin)
    {
        $user = Auth::user();

        if ($izin->user_id === $user->id) {
            $hasBoss = $user->boss() !== null;

            if (!$user->isAdmin() && $hasBoss) {
                return redirect()->route('izin.index')
                    ->with('error', 'Anda tidak diizinkan mengedit data izin milik sendiri.');
            }
        }

        return view('izin.edit', compact('izin'));
    }

    public function update(Request $request, Izin $izin)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_izin' => 'required|in:Satu Hari,Setengah Hari Pagi,Setengah Hari Siang',
            'alasan' => 'required|string',
            'dokumen_pendukung' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:Disetujui,Ditolak,Menunggu',
            'alasan_persetujuan' => 'nullable',
        ]);

        $data = [
            'tanggal' => $request->tanggal,
            'jenis_izin' => $request->jenis_izin,
            'alasan' => $request->alasan,
            'status' => $request->status,
            'alasan_persetujuan' => $request->alasan_persetujuan,
        ];

        $dokumenPendukung = $request->file('dokumen_pendukung');

        if ($dokumenPendukung) {
            if ($izin->dokumen_pendukung && Storage::disk('public')->exists($izin->dokumen_pendukung)) {
                Storage::disk('public')->delete($izin->dokumen_pendukung);
            }
            $namaFile = now()->format('YmdHis') . '.' . $dokumenPendukung->getClientOriginalExtension();
            $path = 'izin/' . $namaFile;
            Storage::disk('public')->put($path, file_get_contents($dokumenPendukung));
            $data['dokumen_pendukung'] = $path;
        }

        $izin->update($data);

        // Setelah update status cuti:

        // Kirim juga email ke atasan (jika ada)
        Mail::to($izin->user->email)->send(new PermohonanDiterimaEmail(
            $izin->user,
            'Izin ' . $request->jenis_izin,
            $request->status,
            $request->alasan_persetujuan
        ));

        $user = Auth::user();
        $boss = $user->boss(); // method ini harus kamu definisikan

        if (!$boss) {
            $boss = $user;
        }

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiketahuiEmail(
                $boss,
                $izin->user,
                'Izin ' . $request->jenis_izin,
                $request->status,
                'Permohonan oleh bawahan Anda telah ' . strtolower($request->status)
            ));
        }

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil diperbarui');
    }

    public function destroy(Izin $izin)
    {
        $izin->delete();

        $namaKaryawan = $izin->user->nama;

        return response()->json([
            'success' => true,
            'message' => "Pengajuan izin milik $namaKaryawan berhasil dihapus"
        ]);
    }
}
