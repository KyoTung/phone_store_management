<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255|unique:users,email',
            'password' =>'required|string|confirmed|min:6',
            // role should not be accepted from user input for security
            'address'=>'string|max:255|nullable',
            'phone'=>'string|nullable'
        ]);

        if($validated->fails()){
            return response()->json($validated->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' =>  $request->email,
                'password' => Hash::make($request->password),
                'role' => 0, // always customer by default
                'address' => $request->has('address') ? $request->address : '',
                'phone' => $request->has('phone') ? $request->phone : '',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user, // ideally wrap this in a Resource
                'message' => 'Register account successfully',
            ], 200);
        } catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!auth()->attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user, // ideally wrap this in a Resource
                'message' => 'Login successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        return response()->json([
            'message' => 'Logout successfully'
        ], 200);
    }
}
