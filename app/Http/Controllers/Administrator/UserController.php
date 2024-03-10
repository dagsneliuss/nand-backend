<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Administrator;

class UserController extends Controller
{
    public function login(Administrator\LoginUserRequest $request) {
        $validated = $request->validated();
        if (!Auth::attempt($validated) || Auth::user()['service_role'] != 'administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email and/or password.'
            ], 401);
        }

        $userResource = new UserResource(Auth::user());

        $tokenExpiresAt = now()->addHour();
        $token = $userResource->createToken(
            $userResource['email_address'], ['administrator'], $tokenExpiresAt
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'data' => [
                'user' => $userResource,
                'token' => $token,
                'token_expires_at' => $tokenExpiresAt
            ]
        ]);
    }

    public function logout(): JsonResponse {
        Auth::user()->tokens()->delete(); // Deletes all tokens due to security concerns.

        return response()->json([
            'success' => true,
            'message' => "You have been logged out."
        ]);
    }
}
