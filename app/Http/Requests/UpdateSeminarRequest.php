<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeminarRequest extends FormRequest
{
    public function authorize() 
    { 
        return true; 
    }

    public function rules()
{
    return [
        'title' => 'sometimes|string|max:255',
        'student_id' => 'sometimes|exists:users,id',
        'scheduled_at' => 'sometimes|date',
        'notes' => 'nullable|string',
        'status' => 'sometimes|in:scheduled,completed,cancelled,pending',
        'examiners' => 'sometimes|array',  // 👈 INI HARUS ADA
        'examiners.*.id' => 'required_with:examiners|exists:users,id',  // 👈 INI JUGA
        'examiners.*.role' => 'sometimes|in:primary,secondary',  // 👈 DAN INI
    ];
}
}