<?php

namespace App\Http\Controllers;

use App\Http\Requests\HakCutiRequest;
use App\Mail\NotifikasiEmail;
use App\Models\HakCuti;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class HakCutiController extends Controller
{
    public function index()
    {
        $hakCuti = HakCuti::first();
        return view('hak_cuti.index', compact('hakCuti'));
    }

    public function store(HakCutiRequest $request)
    {
        $data = [
            'hak_cuti' => $request->hak_cuti,
            'hak_cuti_bersama' => $request->hak_cuti_bersama
        ];

        $hakCuti = HakCuti::first();

        if ($hakCuti) {
            // Jika sudah ada, lakukan update
            $hakCuti->update($data);
            // Setelah update hak_cuti, update nilai sisa_hak_cuti pada semua Cutis
            User::query()->update(['sisa_hak_cuti' => $request->hak_cuti, 'sisa_hak_cuti_bersama' => $request->hak_cuti_bersama]);

            return redirect()->route('hak_cuti.index')->with('success', 'Hak Cuti berhasil diperbarui!');
        } else {
            // Jika belum ada, simpan data baru (hanya untuk pertama kali)
            HakCuti::create($data);
            // Setelah menyimpan hak_cuti, update nilai sisa_hak_cuti pada semua Cutis
            User::query()->update(['sisa_hak_cuti' => $request->hak_cuti, 'sisa_hak_cuti_bersama' => $request->hak_cuti_bersama]);
            return redirect()->route('hak_cuti.index')->with('success', 'Hak Cuti berhasil diset!');
        }
    }
}
