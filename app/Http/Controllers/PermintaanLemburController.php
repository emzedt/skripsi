<?php

namespace App\Http\Controllers;

use App\Mail\PermohonanDiajukanEmail;
use App\Mail\PermohonanDiketahuiEmail;
use App\Mail\PermohonanDiterimaEmail;
use App\Models\PermintaanLembur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PermintaanLemburController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = PermintaanLembur::with('user:id,nama');

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

            return DataTables::of($query)
                ->editColumn('tanggal_mulai', fn($lembur) => Carbon::parse($lembur->tanggal_mulai)->format('d M Y'))
                ->addColumn('aksi', function ($lembur) {
                    $user = Auth::user();
                    $showUrl = route('permintaan_lembur.show', $lembur->id);
                    $editUrl = route('permintaan_lembur.edit', $lembur->id);
                    $deleteFunc = "confirmDelete({$lembur->id}, '{$lembur->user->nama}', '" . Carbon::parse($lembur->tanggal_mulai)->format('d-m-Y') . "')";

                    // Ganti '...' dengan SVG ikon Anda
                    $showIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
                    $editIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
                    $deleteIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';

                    $actions = '<div class="flex space-x-2 justify-center">';
                    $actions .= '<a href="' . $showUrl . '" class="text-black hover:text-gray-600" title="Lihat">' . $showIcon . '</a>';

                    if ($user->hasPermission('Edit Permintaan Lembur') && $lembur->status == 'Menunggu') {
                        $actions .= '<a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Ubah">' . $editIcon . '</a>';
                    }
                    if ($user->hasPermission('Hapus Permintaan Lembur') && $lembur->status == 'Menunggu') {
                        $actions .= '<button type="button" onclick="' . $deleteFunc . '" class="text-red-600 hover:text-red-900" title="Hapus">' . $deleteIcon . '</button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('permintaan_lembur.index');
    }

    public function create()
    {
        $users = User::all();
        $currentUser = Auth::user();

        return view('permintaan_lembur.create', compact('users', 'currentUser'));
    }

    public function show(PermintaanLembur $permintaanLembur)
    {
        $user = $permintaanLembur->user;
        return view('permintaan_lembur.show', compact('permintaanLembur', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'foto_base64' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required',
            'jam_akhir' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $start = strtotime($request->jam_mulai);
                    $end = strtotime($value);

                    // Jika end < start, berarti melewati tengah malam
                    if ($end <= $start) {
                        $end += 86400; // Tambah 24 jam
                    }

                    // Minimal 30 menit lembur
                    if (($end - $start) < 1800) {
                        $fail('Jam akhir harus minimal 30 menit setelah jam mulai');
                    }
                }
            ],
            'tugas' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $fotoBase64 = $request->input('foto_base64');
        $namaFile = null;

        if ($fotoBase64) {
            // Pisahkan metadata dari base64 string
            [$type, $fotoData] = explode(';', $fotoBase64);
            [, $fotoData] = explode(',', $fotoData);

            $fotoData = base64_decode($fotoData);
            $namaFile = now()->format('YmdHis') . '.jpg';
            $path = 'lembur/' . $namaFile;
            Storage::disk('public')->put($path, $fotoData);
            $validated['foto'] = $path;
        }

        // Hitung lama lembur
        $start = strtotime($validated['jam_mulai']);
        $end = strtotime($validated['jam_akhir']);

        if ($end <= $start) {
            $end += 86400; // Tambah 24 jam jika melewati tengah malam
        }

        $validated['lama_lembur'] = ($end - $start) / 60; // Konversi ke menit

        $validated['status'] = 'Menunggu'; // Set default status

        PermintaanLembur::create($validated);

        $users = Auth::user();
        $boss = $users->boss();

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiajukanEmail(
                $boss->nama,
                $users->nama,
                'Permintaan Lembur',
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ));
        } else {
            $admin = User::whereHas('jabatan', function ($q) {
                $q->whereDoesntHave('parentJabatans');
            })->first();

            if ($admin) {
                Mail::to($admin->email)->send(new PermohonanDiajukanEmail(
                    $admin->nama,
                    $users->nama,
                    'Permintaan Lembur',
                    $request->tanggal_mulai,
                    $request->tanggal_selesai
                ));
            }
        }

        return redirect()->route('permintaan_lembur.index')
            ->with('success', 'Permintaan lembur berhasil dibuat');
    }

    public function edit(PermintaanLembur $permintaanLembur)
    {
        $users = User::all();
        $user = $permintaanLembur->user;

        return view('permintaan_lembur.edit', compact('permintaanLembur', 'users', 'user'));
    }

    public function update(Request $request, PermintaanLembur $permintaanLembur)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required',
            'jam_akhir' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $start = strtotime($request->jam_mulai);
                    $end = strtotime($value);

                    // Jika end < start, berarti melewati tengah malam
                    if ($end <= $start) {
                        $end += 86400; // Tambah 24 jam
                    }

                    // Minimal 30 menit lembur
                    if (($end - $start) < 1800) {
                        $fail('Jam akhir harus minimal 30 menit setelah jam mulai');
                    }
                }
            ],
            'tugas' => 'required|string|max:255',
            'status' => 'required|in:Disetujui,Ditolak,Menunggu',
            'foto' => 'nullable',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('foto')) {
            if ($permintaanLembur->foto && Storage::disk('public')->exists('lembur/' . $permintaanLembur->foto)) {
                Storage::disk('public')->delete('lembur/' . $permintaanLembur->foto);
            }
            $foto = $request->file('foto');
            $namaFile = now()->format('YmdHis') . '.' . $foto->getClientOriginalExtension();
            Storage::disk('public')->put('lembur/' . $namaFile, file_get_contents($foto));

            $validated['foto'] = $namaFile;
        }


        // Hitung lama lembur
        $start = strtotime($validated['jam_mulai']);
        $end = strtotime($validated['jam_akhir']);

        if ($end <= $start) {
            $end += 86400; // Tambah 24 jam jika melewati tengah malam
        }

        $validated['lama_lembur'] = ($end - $start) / 60; // Konversi ke menit

        $permintaanLembur->update($validated);

        Mail::to($permintaanLembur->user->email)->send(new PermohonanDiterimaEmail(
            $permintaanLembur->user,
            'Permintaan Lembur',
            $request->status,
            '-'
        ));

        $user = Auth::user();
        $boss = $user->boss(); // method ini harus kamu definisikan

        if (!$boss) {
            $boss = $user;
        }

        if ($boss) {
            Mail::to($boss->email)->send(new PermohonanDiketahuiEmail(
                $boss,
                $permintaanLembur->user,
                'Permintaan Lembur',
                $request->status,
                'Permohonan oleh bawahan Anda telah ' . strtolower($request->status)
            ));
        }

        return redirect()->route('permintaan_lembur.index')
            ->with('success', 'Permintaan lembur berhasil diperbarui');
    }

    public function destroy(PermintaanLembur $permintaanLembur)
    {
        $permintaanLembur->delete();

        // Kirim respons JSON dengan status 200 (OK)
        return response()->json([
            'success' => true,
            'message' => 'Permintaan lembur berhasil dihapus.'
        ], 200);
    }
}
