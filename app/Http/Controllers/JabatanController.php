<?php

namespace App\Http\Controllers;

use App\Http\Requests\JabatanRequest;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request from DataTables
        if ($request->ajax()) {

            $query = Jabatan::query();

            return DataTables::of($query)
                ->addColumn('aksi', function ($jabatan) {
                    // 2. Add the "Aksi" column with Edit and Delete buttons
                    $editUrl = route('jabatan.edit', $jabatan->id);

                    return '
                        <div class="flex space-x-2">
                            <a href="' . $editUrl . '" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button type="button" onclick="confirmDelete(' . $jabatan->id . ', \'' . htmlspecialchars($jabatan->nama, ENT_QUOTES) . '\')" class="text-red-600 hover:text-red-900" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['aksi']) // 3. Tell DataTables the 'aksi' column has HTML
                ->make(true);
        }

        // 4. For the initial page load (non-AJAX), return the view
        return view('jabatan.index');
    }

    public function create()
    {
        $jabatans = Jabatan::select('id', 'nama')
            ->get();

        return view('jabatan.create', compact('jabatans'));
    }

    public function store(JabatanRequest $request)
    {
        $data = [
            'nama' => $request->nama
        ];

        Jabatan::create($data);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan!');
    }

    public function edit(Jabatan $jabatan)
    {
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(JabatanRequest $request, Jabatan $jabatan)
    {
        $data = [
            'nama' => $request->nama
        ];

        $jabatan->update($data);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui!');
    }

    public function destroy(Jabatan $jabatan)
    {
        try {
            $jabatan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus jabatan.'
            ], 500);
        }
    }

    // API endpoint to get hierarchy
    public function getHierarchy(Jabatan $jabatan)
    {
        return response()->json([
            'parent' => $jabatan->parentJabatans()->first(),
            'children' => $jabatan->childJabatans()->get()
        ]);
    }

    // API endpoint to save hierarchy
    public function saveHierarchy(Request $request, Jabatan $jabatan)
    {
        $validate = $request->validate([
            'parent_id' => 'nullable|exists:jabatans,id',
            'children' => 'array',
            'children.*' => 'exists:jabatans,id'
        ]);

        // Delete existing relationships
        DB::table('jabatan_hierarchies')
            ->where('parent_jabatan_id', $jabatan->id)
            ->orWhere('child_jabatan_id', $jabatan->id)
            ->delete();

        // Add parent relationship if exists
        if ($validate['parent_id']) {
            DB::table('jabatan_hierarchies')->insert([
                'parent_jabatan_id' => $validate['parent_id'],
                'child_jabatan_id' => $jabatan->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Add child relationships
        foreach ($validate['children'] as $childId) {
            DB::table('jabatan_hierarchies')->insert([
                'parent_jabatan_id' => $jabatan->id,
                'child_jabatan_id' => $childId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }
}
