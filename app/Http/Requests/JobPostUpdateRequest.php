<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobPostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:100'],
            'type' => ['sometimes', 'in:full-time,part-time'],
            'salary_range' => ['sometimes', 'nullable', 'string', 'max:100'],
            'description' => ['sometimes', 'string'],
            'requirements' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:open,closed'],
        ];
    }
}
