<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'nama' => 'required',
            'email' => 'required',
            'foto_face_recognition'  => 'nullable|mimes:png,jpg,jpeg',
            'no_hp' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'sisa_hak_cuti' => 'nullable|integer|min:0',
            'sisa_hak_cuti_bersama' => 'nullable|integer|min:0',
            'password' => $this->isMethod('post') ? 'required|min:8' : 'nullable|min:8',
            'jabatan_id' => 'required|exists:jabatans,id',
            'status_karyawan_id' => 'required|exists:status_karyawans,id',
            'hak_cuti_id' => $this->isMethod('post') ? 'required|exists:hak_cutis,id' : 'nullable|exists:hak_cutis,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama karyawan wajib diisi.',
            'email.required' => 'Email karyawan wajib diisi.',
            'password.required' => 'Kata sandi karyawan wajib diisi.',
            'password.min' => 'Kata sandi harus terdiri dari minimal 8 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_rekening.required' => 'Nomor Rekening wajib ada.',
            'sisa_hak_cuti.required' => 'Sisa hak cuti wajib diisi.',
            'sisa_hak_cuti.integer' => 'Sisa hak cuti harus berupa angka.',
            'sisa_hak_cuti.min' => 'Sisa hak cuti tidak boleh kurang dari 0.',
            'sisa_hak_cuti_bersama.required' => 'Sisa hak cuti wajib diisi.',
            'sisa_hak_cuti_bersama.integer' => 'Sisa hak cuti harus berupa angka.',
            'sisa_hak_cuti_bersama.min' => 'Sisa hak cuti tidak boleh kurang dari 0.',
            'jabatan_id.required' => 'Jabatan harus ada.',
            'jabatan_id.numeric' => 'Jabatan ID harus berupa angka.',
            'hak_cuti_id.numeric' => 'Jabatan ID harus berupa angka.'
        ];
    }
}
