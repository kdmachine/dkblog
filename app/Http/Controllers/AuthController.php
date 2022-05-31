<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        return $request->user();
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => "User not exist!",
                ], 404
            );
        } elseif (!Hash::check($request->password, $user->password ,[])) {
            return response()->json(
                [
                    'message' => "Wrong password!",
                ], 404
            );
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'access_token' => $token,
            'type_token' => 'Bearer',
            'message' => 'Login successfully!',
            'email' => $request->email,
            'password' => $request->password,
        ];
    }

    public function register(Request $request)
    {
        $messages = [
            'email.email' => "Error email!",
            // 'password.password' => "Error password!",
            'email.required' => 'Email required!',
            'password.required' => 'Password required!',
        ];

        $validate = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ], $messages);

        if ($validate->fails()) {
            return response()->json(
                [
                    'message' => $validate->errors()
                ], 404
            );
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(
            [
                'message' => 'Account created!',
            ], 200
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                'message' => 'Logged out!',
            ], 200
        );
    }
}
