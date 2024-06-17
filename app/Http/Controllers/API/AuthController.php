<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'username' => 'required|string|lowercase|max:255|unique:users',
                'email' => 'required|string|lowercase|email|max:255|unique:users',
                'password' => 'required|string|min:5',
                'passwordConfirmation' => 'required|same:password',
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->save();

            return response()->json(['message' => 'Create new user successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'emailOrUsername' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$loginType => $request->emailOrUsername, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->tokens()->delete();

            $token = $user->createToken('Bearer')->plainTextToken;

            $user->save();

            return response()->json(['token' => $token]);
        } else {
            return response()->json(['message' => 'Email/Username atau Password Salah'], 422);
        }
    }

    public function logout()
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
