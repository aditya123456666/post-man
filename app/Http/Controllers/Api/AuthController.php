<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user->only(['id', 'name', 'email']),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Kredensial yang diberikan salah.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user->only(['id', 'name', 'email']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
