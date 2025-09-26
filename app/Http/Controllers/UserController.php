<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //

      public function register(Request $request)
    {
        $request->validate([
            "name"=> "required|string",
            "email"=> "required|email|unique:users,email",
            "password" => 'required|min:8|regex:/[A-Za-z]/|regex:/[0-9]/',  // Password validation
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> $request->password
        ]);

        return response()->json([
            'message' => 'Registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }

    // public function logoutAll(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Logged out from all devices',
    //     ]);
    // }

}
