<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CutiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_cuti' => 'nullable|string|max:255',
            'jenis_cuti' => 'nullable|string|in:Cuti Biasa,Cuti Spesial', // sesuaikan enum
            'tanggal_mulai_cuti' => 'nullable|date',
            'tanggal_selesai_cuti' => 'nullable|date|after_or_equal:tanggal_mulai_cuti',
            'alasan_cuti' => 'nullable|string|max:500',
            'foto_cuti' => 'nullable|image|mimes:jpg,jpeg,png',
            'status' => 'nullable|string|in:Disetujui,Ditolak', // sesuaikan enum
            'alasan_persetujuan_cuti' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_cuti.required' => 'Nama cuti wajib diisi.',
            'nama_cuti.string' => 'Nama cuti harus berupa teks.',
            'nama_cuti.max' => 'Nama cuti tidak boleh lebih dari 255 karakter.',

            'jenis_cuti.required' => 'Jenis cuti wajib diisi.',

            'tanggal_mulai_cuti.required' => 'Tanggal mulai cuti wajib diisi.',
            'tanggal_mulai_cuti.date' => 'Format tanggal mulai cuti tidak valid.',

            'tanggal_selesai_cuti.required' => 'Tanggal selesai cuti wajib diisi.',
            'tanggal_selesai_cuti.date' => 'Format tanggal selesai cuti tidak valid.',
            'tanggal_selesai_cuti.after_or_equal' => 'Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai.',

            'alasan_cuti.required' => 'Alasan cuti wajib diisi.',
            'alasan_cuti.string' => 'Alasan cuti harus berupa teks.',
            'alasan_cuti.max' => 'Alasan cuti tidak boleh lebih dari 500 karakter.',

            'foto_cuti.image' => 'File yang diunggah harus berupa gambar.',
            'foto_cuti.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',

            'status.in' => 'Status harus salah satu dari: Disetujui, atau Ditolak.',

            'alasan_persetujuan_cuti.string' => 'Alasan persetujuan cuti harus berupa teks.',
            'alasan_persetujuan_cuti.max' => 'Alasan persetujuan cuti tidak boleh lebih dari 500 karakter.',
        ];
    }
}
