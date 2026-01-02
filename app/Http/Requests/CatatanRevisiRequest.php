<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatatanRevisiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'perbaikan_wajib' => 'nullable|array',
            'perbaikan_wajib.*' => 'nullable|string|max:500',
            'saran_perbaikan' => 'nullable|array',
            'saran_perbaikan.*' => 'nullable|string|max:500',
            'komentar_umum' => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'perbaikan_wajib.array' => 'Perbaikan wajib harus berupa array',
            'perbaikan_wajib.*.string' => 'Setiap perbaikan wajib harus berupa teks',
            'perbaikan_wajib.*.max' => 'Perbaikan wajib maksimal 500 karakter',
            'saran_perbaikan.array' => 'Saran perbaikan harus berupa array',
            'saran_perbaikan.*.string' => 'Setiap saran perbaikan harus berupa teks',
            'saran_perbaikan.*.max' => 'Saran perbaikan maksimal 500 karakter',
            'komentar_umum.max' => 'Komentar umum maksimal 2000 karakter',
        ];
    }
}