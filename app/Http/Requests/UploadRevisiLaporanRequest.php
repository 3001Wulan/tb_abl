<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRevisiLaporanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file_laporan' => 'required|file|mimes:pdf|max:10240',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'file_laporan.required' => 'File laporan harus diupload',
            'file_laporan.file' => 'File laporan harus berupa file',
            'file_laporan.mimes' => 'File harus berformat PDF',
            'file_laporan.max' => 'Ukuran file maksimal 10MB',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
        ];
    }
}