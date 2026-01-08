<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Register new user and return token
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    // Login user and return token
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    // Return authenticated user info
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    // Logout (revoke token)
    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        // Revoke refresh tokens too (optional)
        app(RefreshTokenRepository::class)->revokeRefreshTokensByAccessTokenId($token->id);

        return response()->json(['message' => 'Logged out']);
    }
}