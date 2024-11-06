<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    //
    public function getproductlist(){
        $products = Product::with('photoproduct')-> get();
        return response()->json(['products' => $products]);
    }
    public function getproductbyid($id){
        $product = Product::with('photoproduct')-> where('slug',$id)->get();
        return response()->json(['product' => $product]);
    }
    public function getproductbycategoryid($id){
        $product = Product::with('photoproduct')-> where('cat_id', $id)->get();
        return response()->json(['product' => $product]);
    }

    public function getis_featuredproduct(){
        $product = Product::with('photoproduct')-> where('is_featured', 1)->get();
        return response()->json(['product' => $product]);
    }

}
