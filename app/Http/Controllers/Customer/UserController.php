<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Customer\RegisterUserRequest $request): JsonResponse {
        $userResource = new UserResource(User::create($request->validated()));

        $tokenExpiresAt = now()->addWeek();
        $token = $userResource->createToken(
            $userResource['email_address'], ['*'], $tokenExpiresAt
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'user' => $userResource,
                'token' => $token,
                'token_expires_at' => $tokenExpiresAt
            ]
        ], 201);
    }

    public function login(Customer\LoginUserRequest $request): JsonResponse {
        $validated = $request->validated();
        if (!Auth::attempt($validated)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email and/or password.'
            ], 401);
        }

        $userResource = new UserResource(Auth::user());

        $tokenExpiresAt = now()->addWeek();
        $token = $userResource->createToken(
            $userResource['email_address'], ['customer'], $tokenExpiresAt
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
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => "You have been logged out."
        ]);
    }

    public function update(Customer\UpdateUserRequest $request): JsonResponse {
        Auth::user()->update($request->validated());

        $userResource = new UserResource(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => [
                'user' => $userResource
            ]
        ]);
    }

    public function delete(): JsonResponse {
        Auth::user()->delete();
        Auth::user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
