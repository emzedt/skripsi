<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiSalesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'foto_base64' => 'nullable|string',
            'deskripsi' => 'required|string|max:255',
            'status' => 'required|in:Titip Brosur,Meeting',
            // 'status_persetujuan' => 'nullable|in:Disetujui, Ditolak, Menunggu',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'jam.required' => 'Jam wajib diisi',
            'jam.date_format' => 'Format jam harus HH:MM',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'deskripsi.string' => 'Deskripsi harus berupa teks',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid'
        ];
    }
}
