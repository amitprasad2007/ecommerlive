<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class OrderController extends Controller
{
    public function savecart(Request $request){
        //dd($request)

        return response()->json(['user' => Auth::user()]);
    }
}
