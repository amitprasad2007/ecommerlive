<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userlogin(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            $user =  User::create([
                'mobile' => $request->mobile,
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make('123456')
            ]); 
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json(['token' => $token]);
            }    
        } else {
            if ($request->password) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('authToken')->plainTextToken;
                    return response()->json(['token' => $token]);
                } else {
                    return response()->json(['message' => 'Invalid email or password'], 401);
                }
            }
            return response()->json(['message' => 'please send password'], 200);   
        }
    }

    public function userauth(Request $request){

        return response()->json(['user' => auth()->user()]);
    }
}
