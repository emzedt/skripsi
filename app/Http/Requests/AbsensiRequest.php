<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiRequest extends FormRequest
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
        // Untuk endpoint check-in (masuk)
        if ($this->is('absensi-masuk') || $this->is('absensi-masuk/*')) {
            return [
                'foto_masuk' => 'required|string',
                'latitude_masuk' => 'nullable|numeric',
                'longitude_masuk' => 'nullable|numeric',
            ];
        }

        // Untuk endpoint check-out (keluar)
        if ($this->is('absensi-keluar') || $this->is('absensi-keluar/*')) {
            return [
                'foto_keluar' => 'required|string',
                'latitude_keluar' => 'nullable|numeric',
                'longitude_keluar' => 'nullable|numeric',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'foto_masuk.required' => 'Foto masuk wajib diunggah!',
            'foto_keluar.image' => 'Foto keluar harus berupa gambar!',
            'jam_masuk.required' => 'Jam masuk harus diisi!',
            'status.in' => 'Status harus salah satu dari: Hadir, Alfa, Izin, Cuti, Telat, Pulang Cepat.',
        ];
    }
}
