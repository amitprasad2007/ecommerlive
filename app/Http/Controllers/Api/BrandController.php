<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function getbrandlist(){
        $brands = Brand::all();
        $result =  $brands->map(function($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->title,
                'slug'=> $brand->slug,
                'logo' => $brand->photo ? asset('storage/photos/1/Brands/'.$brand->photo) : null
            ];
        });
        return response()->json($result);
    }
}
