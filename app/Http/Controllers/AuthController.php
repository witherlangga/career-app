<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordForgotRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
        ]);

        $user->profile()->create();

        $abilities = $this->abilitiesForRole($user->role);
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $abilities = $this->abilitiesForRole($user->role);
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()->load('profile')]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out from all devices']);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        $abilities = $this->abilitiesForRole($user->role);
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
        ]);
    }

    public function forgotPassword(PasswordForgotRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => __($status),
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'message' => __($status),
        ], $status === Password::PASSWORD_RESET ? 200 : 422);
    }

    /**
     * @return array<int, string>
     */
    private function abilitiesForRole(string $role): array
    {
        return match ($role) {
            User::ROLE_EMPLOYER => ['jobs:write', 'applications:read', 'profile:write'],
            User::ROLE_WORKER => ['applications:write', 'profile:write'],
            default => [],
        };
    }
}
