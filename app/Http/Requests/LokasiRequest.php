<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LokasiRequest extends FormRequest
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
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ];
    }
    public function messages()
    {
        return [
            'nama.required' => 'Nama lokasi wajib diisi.',
            'nama.string' => 'Nama lokasi harus berupa teks.',
            'nama.max' => 'Nama lokasi maksimal 255 karakter.',
            'latitude.required' => 'Latitude wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 hingga 90.',
            'longitude.required' => 'Longitude wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 hingga 180.',
            'radius.required' => 'Radius wajib diisi.',
            'radius.integer' => 'Radius harus berupa angka bulat.',
            'radius.min' => 'Radius harus minimal 1.',
        ];
    }
}
