<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userlogin(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {

        }
        return response()->json($user);
    }
}
