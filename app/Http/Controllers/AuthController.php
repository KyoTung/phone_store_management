<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

       $validated = Validator::make($request -> all(),[
         'name' =>'required|string|max:255',
         'email' =>'required|string|email|max:255|unique:users, email',
         'password' =>'required|string|confirmed|min:6',
         'role'=>'integer',
         'address'=>'string|max:255',
         'phone'=>'integer'
     ]);

        if($validated->fails()){
           return response()->json($validated->errors(), status: 403);
       }

        // role co 2 gia tri la 0, 1 va 2
        // 0 ung voi khach hang va 1 ung voi nhan vien, 2 ung voi quan tri vien
        // quan tri vien khong phai dang ky tai khoan bang cach thong thuong nhu khach hang va nhan vien
        // khi tao tai khoan mac dinh role se la 0, rieng nhan vien se duoc sua quyen sau trong ql tai khoan

        try {
            $user = User::create([
                'name' => $request->name,
                'email' =>  $request->email,
                'password' =>Hash::make( $request->password),
                'role'=> $request->input('role', 0)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'message' =>'Register account successfully',
            ], status: 200);
        } catch (\Exception $exception){
            return response()->json([
               'error'=>$exception->getMessage()
            ], status: 403);
        }
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), status: 403);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        try {
            if (!auth()->attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], status: 403);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $tokenResult = $user->createToken('auth_token');
            $token = $tokenResult->plainTextToken;


//            $tokenResult->accessToken->expires_at = now()->addHours(8);
//            $tokenResult->accessToken->save();

            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'message' => 'Login successfully',
            ], status: 200);
        } catch (\Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], status: 403);
        }
    }

    public function logout(Request $request){

            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logout successfully'
            ], 200);

    }


}
