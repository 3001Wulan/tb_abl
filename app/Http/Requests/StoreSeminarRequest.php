<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeminarRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'student_id' => 'required|exists:users,id',
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
            'examiners' => 'nullable|array',
            'examiners.*.id' => 'required_with:examiners|exists:users,id',
            'examiners.*.role' => 'in:primary,secondary',
        ];
    }
}
