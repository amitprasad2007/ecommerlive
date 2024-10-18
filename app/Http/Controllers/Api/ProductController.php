<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    //
    public function getproductlist(){
        $products = Product::getAllProduct();
        return response()->json(['products' => $products]);
    }
    public function getproductbyid($id){
        $product = Product::find($id)->with('photostring') ;
        return response()->json(['product' => $product]);
    }
    public function getproductbycategoryid($id){
        $product = Product::where('cat_id', $id)->get();
        return response()->json(['product' => $product]);
    }

    public function getis_featuredproduct(){
        $product = Product::where('is_featured', 1)->get();
        return response()->json(['product' => $product]);
    }

}
