<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function savecart(Request $request){
        //dd($request)

        return response()->json(['req' => $request->cart]);
    }
}
