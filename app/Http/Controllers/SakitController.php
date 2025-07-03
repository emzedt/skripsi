<?php
// app/Http/Controllers/SakitController.php
namespace App\Http\Controllers;

use App\Mail\PermohonanDiajukanEmail;
use App\Mail\PermohonanDiketahuiEmail;
use App\Mail\PermohonanDiterimaEmail;
use App\Models\Sakit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SakitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Query dasar dengan relasi ke user
            $query = Sakit::with('user:id,nama');

            // Terapkan logika hak akses
            $user = Auth::user();
            if (!$user->isAdmin()) {
                $query->where(function ($q) use ($user) {
                    // Tampilkan data milik user itu sendiri
                    $q->where('user_id', $user->id);

                    // Jika user adalah seorang manajer, tampilkan juga data bawahannya
                    if ($user->jabatan && $user->jabatan->childJabatans()->exists()) {
                        $subordinateIds = $user->subordinates()->pluck('users.id');
                        $q->orWhereIn('user_id', $subordinateIds);
                    }
                });
            }

            // Serahkan query yang sudah difilter ke DataTables
            return DataTables::of($query)
                ->editColumn('tanggal_mulai', fn($sakit) => Carbon::parse($sakit->tanggal_mulai)->format('d M Y'))
                ->editColumn('tanggal_selesai', fn($sakit) => Carbon::parse($sakit->tanggal_selesai)->format('d M Y'))
                ->addColumn('aksi', function ($sakit) {
                    // Tombol aksi (Edit & Hapus)
                    $editUrl = route('sakit.edit', $sakit->id);
                    $deleteFunc = "confirmDelete({$sakit->id}, '{$sakit->user->nama}')";

                    // Ganti '...' dengan SVG ikon Anda jika ada
                    $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                    $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';

                    $actions = '<div class="flex space-x-2 justify-center">';
                    // Tombol edit bisa ditambahkan dengan permission check jika perlu
                    $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Ubah">' . $editIcon . '</a>';
                    // Tombol hapus hanya jika status masih menunggu
                    if ($sakit->status == 'Menunggu') {
                        $actions .= '<button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('sakit.index');
    }

    public function create()
    {
        return view('sakit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'diagnosa' => 'required|string|max:255',
            'surat_dokter' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $suratDokter = $request->file('surat_dokter');
        $namaFile = null;

        if ($suratDokter) {
            $namaFile = now()->format('YmdHis') . '.' . $suratDokter->getClientOriginalExtension();
            $path = 'sakit/' . $namaFile;
            Storage::disk('public')->put($path, file_get_contents($suratDokter));
        }

        Sakit::create([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'diagnosa' => $request->diagnosa,
            'surat_dokter' => $path,
            'status' => 'Menunggu',
            'user_id' => Auth::id(),
        ]);

        $user = Auth::user();
        $boss = $user->boss();

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiajukanEmail(
                $boss->nama,
                $user->nama,
                'Sakit',
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ));
        } else {
            // fallback ke HRD kalau tidak punya boss
            Mail::to($user->email)->send(new PermohonanDiajukanEmail(
                $user->nama,
                $user->nama,
                'Sakit',
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ));
        }

        return redirect()->route('sakit.index')->with('success', 'Pengajuan sakit berhasil dikirim');
    }

    public function show(Sakit $sakit)
    {
        return view('sakit.show', compact('sakit'));
    }

    public function edit(Sakit $sakit)
    {
        $user = Auth::user();

        if ($sakit->user_id === $user->id) {
            $hasBoss = $user->boss() !== null;

            if (!$user->isAdmin() && $hasBoss) {
                return redirect()->route('sakit.index')
                    ->with('error', 'Anda tidak diizinkan mengedit data sakit milik sendiri.');
            }
        }

        return view('sakit.edit', compact('sakit'));
    }

    public function update(Request $request, Sakit $sakit)
    {
        $request->validate([

            'status' => 'nullable|in:Disetujui,Ditolak,Menunggu',
            'alasan_persetujuan' => 'nullable',
        ]);

        $data = [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'diagnosa' => $request->diagnosa,
            'status' => $request->status,
            'alasan_persetujuan' => $request->alasan_persetujuan
        ];

        $suratDokter = $request->file('surat_dokter');

        if ($suratDokter) {
            if ($sakit->surat_dokter && Storage::disk('public')->exists($sakit->surat_dokter)) {
                Storage::disk('public')->delete($sakit->surat_dokter);
            }
            $namaFile = now()->format('YmdHis') . '.' . $suratDokter->getClientOriginalExtension();
            $path = 'sakit/' . $namaFile;
            Storage::disk('public')->put($path, file_get_contents($suratDokter));
            $data['surat_dokter'] = $path;
        }

        $sakit->update($data);

        Mail::to($sakit->user->email)->send(new PermohonanDiterimaEmail(
            $sakit->user,
            'Sakit',
            $request->status,
            $request->alasan_persetujuan
        ));

        $boss = $sakit->user->boss(); // method ini harus kamu definisikan
        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiketahuiEmail(
                $boss,
                $sakit->user,
                'Sakit',
                $request->status,
                'Permohonan oleh bawahan Anda telah ' . strtolower($request->status)
            ));
        }

        return redirect()->route('sakit.index')->with('success', 'Status pengajuan sakit berhasil diperbarui');
    }

    public function destroy(Sakit $sakit)
    {
        $sakit->delete();

        $namaKaryawan = $sakit->user->nama;

        return response()->json([
            'success' => true,
            'message' => "Pengajuan sakit milik $namaKaryawan berhasil dihapus"
        ]);
    }
}
