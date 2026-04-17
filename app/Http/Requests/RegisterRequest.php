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
            'name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
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

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Ensure name is always set, using company_name if needed
        if (!isset($validated['name']) || empty($validated['name'])) {
            $validated['name'] = $validated['company_name'] ?? 'User ' . uniqid();
        }
        
        return $validated;
    }
}
