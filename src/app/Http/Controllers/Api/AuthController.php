<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'token_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->assignRole(Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => 'web',
        ]));

        [$token, $plainTextToken] = ApiToken::issueFor($user, $validated['token_name'] ?? 'api-register');

        return response()->json([
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user->load('roles')),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'token_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak cocok.'],
            ]);
        }

        [$token, $plainTextToken] = ApiToken::issueFor($user, $validated['token_name'] ?? 'api-login');

        return response()->json([
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user->load('roles')),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->userPayload($request->user()->load('roles')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('api_token')?->delete();

        return response()->json([
            'message' => 'Token revoked.',
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values(),
        ];
    }
}
