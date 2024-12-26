<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $newUser = User::create($request->all());

        $user = User::with('driver')->findOrFail($newUser->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::with('driver')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    public function logout()
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        $user = User::with('driver')->findOrFail(auth()->user()->id);

        $user->update([
            'avatar' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => $user,
        ], 200);
    }

    public function editProfile(Request $request)
    {
        $user = User::with('driver')->findOrFail(auth()->user()->id);
        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ]);

        $user = User::with('driver')->findOrFail(auth()->user()->id);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password',
            ], 401);
        }

        $user->update([
            'password' => $request->new_password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => $user,
        ], 200);
    }
}
