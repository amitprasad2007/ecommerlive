<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function getbrandlist(){
        $brands = Brand::all();
        return response()->json(['brands' => $brands]);
    }
}
