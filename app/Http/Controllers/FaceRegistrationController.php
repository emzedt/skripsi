<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FaceRegistrationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!empty($user->foto_face_recognition)) {
            return redirect()->route('dashboard')->with('false', 'Anda sudah mendaftarkan wajah.');
        }

        return view('absensi.face', compact('user'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'face_image' => 'required|string', // Data URL gambar
        ]);

        try {
            $user = Auth::user();

            // Konversi data URL ke file gambar
            $image_parts = explode(";base64,", $request->face_image);
            $image_base64 = base64_decode($image_parts[1]);

            // Buat nama file unik
            $fileName = 'face_' . $user->id . '_' . time() . '.png';
            $path = 'face_images/' . $fileName;

            // Simpan gambar ke storage/app/public/face_images
            Storage::disk('public')->put($path, $image_base64);

            $fotoFaceRecognition = User::select('id', 'foto_face_recognition')->where('id', $user->id)->first();
            // Simpan path gambar ke database
            $fotoFaceRecognition->update([
                'foto_face_recognition' => $path, // public path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil didaftarkan',
                'image_path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wajah: ' . $e->getMessage()
            ], 500);
        }
    }


    public function ajaxDescrip(Request $request)
    {
        try {
            $request->validate([
                'myData' => 'required|array',
                'user_id' => 'required|exists:users,id',
                'image' => 'required',
                'path' => 'required'
            ]);

            $user = User::findOrFail($request->user_id);
            $filePath = public_path('neural.json');

            // Read existing data
            $existingData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
            if (!is_array($existingData)) {
                $existingData = [];
            }

            // Check if user already has descriptors
            $userIndex = null;
            foreach ($existingData as $index => $entry) {
                if ($entry['user_id'] == $user->id) {
                    $userIndex = $index;
                    break;
                }
            }

            // Prepare new descriptor data
            $newDescriptor = [
                'label' => $user->nama . '_' . $user->id,
                'descriptors' => $request->myData['descriptors'],
                'user_id' => $user->id,
                'registered_at' => now()->toDateTimeString()
            ];

            if ($userIndex !== null) {
                // Merge new descriptors with existing ones
                $existingDescriptors = $existingData[$userIndex]['descriptors'];
                $newDescriptors = array_merge($existingDescriptors, $newDescriptor['descriptors']);
                $newDescriptor['descriptors'] = $newDescriptors;

                // Replace the old entry
                $existingData[$userIndex] = $newDescriptor;
            } else {
                // Add new entry
                $existingData[] = $newDescriptor;
            }

            // Save to file
            file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Face descriptor updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : []
            ], 500);
        }
    }
}
