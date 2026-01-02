<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_done' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'is_done.required' => 'Status progress harus diisi',
            'is_done.boolean' => 'Status progress harus berupa true/false',
        ];
    }
}