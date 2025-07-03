<?php

namespace App\Http\Controllers;

use App\Models\Kalender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderController extends Controller
{
    public function index()
    {
        return view('kalender.index');
    }

    public function getHariLibur(Request $request)
    {
        $query = Kalender::query();

        if ($request->withTrashed) {
            $query->withTrashed();
        }

        $libur = $query->get()->map(function ($item) {
            // Default colors for active items
            $color = '#ef4444'; // Red for regular Libur
            $textColor = '#ffffff';

            if ($item->trashed()) {
                $color = '#9ca3af';
                $textColor = '#ffffff';
            } elseif ($item->jenis_libur === 'Cuti Bersama') {
                $color = '#Ffb6c1';
            }

            return [
                'id' => $item->id,
                'title' => $item->keterangan,
                'start' => $item->tanggal->format('Y-m-d'),
                'allDay' => true,
                'color' => $color,
                'textColor' => $textColor,
                'className' => $item->trashed() ? 'libur-deleted' : ($item->jenis_libur === 'Cuti Bersama' ? 'libur-cuti-bersama' : 'libur-aktif'),
                'extendedProps' => [
                    'jenis_libur' => $item->jenis_libur,
                    'deleted_at' => $item->deleted_at
                ]
            ];
        });

        return response()->json($libur);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jenis_libur' => 'required|in:Cuti Bersama,Libur',
        ]);

        // Cek apakah sudah ada (termasuk yang soft delete)
        $existing = Kalender::withTrashed()
            ->where('tanggal', $validate['tanggal'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restore yang soft delete
                $existing->restore();
                $existing->update(['keterangan' => $validate['keterangan'], 'jenis_libur' => $validate['jenis_libur']]);
                return response()->json([
                    'success' => true,
                    'data' => $existing,
                    'message' => 'Hari libur berhasil diaktifkan kembali'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal tersebut sudah ada hari libur'
                ], 422);
            }
        }

        $libur = Kalender::create([
            'tanggal' => $validate['tanggal'],
            'keterangan' => $validate['keterangan'],
            'jenis_libur' => $validate['jenis_libur'],
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'data' => $libur
        ]);
    }

    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jenis_libur' => 'required|in:Cuti Bersama,Libur'
        ]);

        // Cari data termasuk yang soft delete
        $libur = Kalender::withTrashed()->findOrFail($id);

        // Jika data di soft delete, restore dulu
        if ($libur->trashed()) {
            $libur->restore();
        }

        // Update data
        $libur->update([
            'tanggal' => $validate['tanggal'],
            'keterangan' => $validate['keterangan'],
            'jenis_libur' => $validate['jenis_libur'],
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'data' => $libur,
            'message' => $libur->trashed() ?
                'Hari libur diaktifkan kembali dan diperbarui' :
                'Hari libur berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        try {
            $libur = Kalender::findOrFail($id);
            $libur->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hari libur'
            ], 500);
        }
    }
}
