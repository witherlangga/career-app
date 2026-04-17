<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:' . implode(',', [
                User::ROLE_EMPLOYER,
                User::ROLE_WORKER,
            ])],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }
}
