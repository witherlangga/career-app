<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobPostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:full-time,part-time,Full-time,Part-time,Contract,Freelance,Internship'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,closed'],
        ];
    }

    protected function passedValidation()
    {
        // Normalize employment_type to type if needed
        if (isset($this->employment_type) && !isset($this->type)) {
            $typeMap = [
                'Full-time' => 'full-time',
                'Part-time' => 'part-time',
                'Contract' => 'full-time',
                'Freelance' => 'part-time',
                'Internship' => 'part-time',
            ];
            $type = $typeMap[$this->employment_type] ?? 'full-time';
            $this->merge(['type' => $type]);
        }
        
        // Set default category if not provided
        if (!isset($this->category)) {
            $this->merge(['category' => 'General']);
        }
        
        // Set default status
        if (!isset($this->status)) {
            $this->merge(['status' => 'open']);
        }
    }
}
