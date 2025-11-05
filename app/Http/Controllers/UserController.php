<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

      public function register(Request $request)
        {
            try{
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

            //Assign role
            $user->assignRole('user');

            return response()->json([
                'message' => 'Registered successfully',
                'user' => $user,
            ], 201);
        } catch(Exception $e){
            return response()->json([
                'message' => 'An error Ocuured. Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    try {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'status' => 401
            ]);
        }

        
        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'status' => 200
        ]);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred. Try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
     public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // public function logout(Request $request)
    // {
    //     // Revoke the token that was used to authenticate the current request
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Logged out successfully',
    //     ]);
    // }

    // public function logoutAll(Request $request)
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Logged out from all devices',
    //     ]);
    // }


    public function agents(){
        try{
             $agents = User::whereHas('roles', function ($query) {
            $query->where('name', 'agent');
            })->get();

            return response()->json($agents);
        }catch(Exception $e){
            return response()->json([
                'message' => 'An error Ocuured. Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
      
    }

    public function search($search){
        
        $user = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->first();

            return response()->json($user);
    }

    public function addAgent(Request $request)
    {
        try{
            $user = User::findOrFail($request->user_id);
            $user->roles()->syncWithoutDetaching([Role::where('name', 'agent')->first()->id]);

            return response()->json(['message' => 'User added as agent successfully.']);
        }catch(Exception $e){
            return response()->json([
                'message' => 'An error Ocuured. Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function removeAgent($id)
    {
        try{
            $user = User::findOrFail($id);
            $agentRole = Role::where('name', 'agent')->first();

            $user->roles()->detach($agentRole->id);

            return response()->json(['message' => "{$user->name} removed as agent"]);
        }catch(Exception $e){
            return response()->json([
                'message' => 'An error Ocuured. Try again later',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

}
