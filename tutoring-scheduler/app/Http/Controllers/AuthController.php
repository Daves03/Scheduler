<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tutor;

class AuthController extends Controller
{
    // REGISTER (Create Account)
public function register(Request $request)
    {
        // 1. Validate Basic User Info
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:admin,student,tutor'
        ];

        // 2. Add extra validation for Tutors
        if ($request->role === 'tutor') {
            $rules['phone'] = 'required|string'; // ✅ VALIDATE PHONE
            $rules['specialization'] = 'required|string';
            $rules['hourly_rate'] = 'required|numeric';
            $rules['bio'] = 'required|string';
        }

        $fields = $request->validate($rules);

        // 3. Create User Account
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => $fields['role']
        ]);

        // 4. Create Tutor Profile
        if ($fields['role'] === 'tutor') {
            $subjectsArray = array_map('trim', explode(',', $fields['specialization']));

            Tutor::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'phone' => $fields['phone'], // ✅ SAVE PHONE
                'bio' => $fields['bio'],
                'hourly_rate' => $fields['hourly_rate'],
                'subjects' => $subjectsArray
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
            'message' => 'Account created successfully'
        ], 201);
    }
    // LOGIN
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($fields)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $fields['email'])->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}