<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function savecart(Request $request){
        return response()->json(['user' => Auth::user()]);
    }
}
