<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)->mixedCase()],
        ]);
        if($validator->fails()){
            return response([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 202);
        }
        
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            return response([
                'status' => 'success',
                'data' => [
                    'email' => $user->email,
                    'token' => $user->createToken('api-token')->accessToken,
                ],
            ], 200);
        }else {
            return response([
                'status' => 'error',
                'message' => 'Unauthorized Access',
            ], 203);
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        if($validator->fails()){
            return response([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 401);
        }
        $validatedData = $request->all();
        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);
        
        return response([
            'status' => 'success',
            'data' => [
                'email' => $user->email,
                'token' => $user->createToken('api-token')->accessToken,
            ]
        ], 200);
    }

    public function logout(){
        if(Auth::check()){
            $user = Auth::user()->token();
            $user->revoke();
            return response([
                'status' => 'success',
                'message' => 'Logout Successfully',
                'data' => $user,
            ], 200);
        }
        return response([
            'status' => 'error',
            'message' => 'You are not logged in' 
        ], 401);
    }
}
