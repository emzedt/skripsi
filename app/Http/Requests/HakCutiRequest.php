<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HakCutiRequest extends FormRequest
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
            'hak_cuti' => 'required|integer',
            'hak_cuti_bersama' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'hak_cuti.required' => 'Hak cuti wajib ada.',
            'hak_cuti_bersama.required' => 'Hak cuti Bersama wajib ada.'
        ];
    }
}
