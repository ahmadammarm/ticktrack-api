<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|same:password',
            ]);

            $data['password'] = bcrypt($data['password']);
            $user = \App\Models\User::create($data);

            $user->role = 'user';
            $user->save();

            return response()->json([
                'message' => 'User registered successfully',
                'data' => new UserResource($user)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed due to an error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login failed due to an error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = Auth::user();
            return response()->json([
                'message' => 'User profile retrieved successfully',
                'data' => new UserResource($user)
            ], 200);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user profile due to an error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed due to an error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
