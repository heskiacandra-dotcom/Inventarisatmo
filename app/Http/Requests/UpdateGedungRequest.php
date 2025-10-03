<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGedungRequest extends FormRequest
{
    protected $errorBag = 'update';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'kode' => [
                'required',
                'string',
                'max:5',
                Rule::unique('gedung', 'kode')->ignore($this->gedung) // abaikan kode milik dirinya sendiri
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kolom nama wajib diisi!',
            'name.string' => 'Kolom nama harus berupa karakter!',
            'name.min' => 'Kolom nama minimal :min karakter!',
            'name.max' => 'Kolom nama maksimal :max karakter!',

            'kode.required' => 'Kolom kode wajib diisi!',
            'kode.string' => 'Kolom kode harus berupa karakter!',
            'kode.max' => 'Kolom kode maksimal :max karakter!',
            'kode.unique' => 'Kode gedung sudah digunakan!',
        ];
    }
}
