<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:50'],
        ];
    }
}
