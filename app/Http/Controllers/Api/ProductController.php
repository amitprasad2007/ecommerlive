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
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        return response()->json(['product' => $product,'recent_products' => $recent_products]);
    }

    public function getis_featuredproduct(){
        $product = Product::with('photoproduct')-> where('is_featured', 1)->get();
        return response()->json(['product' => $product]);
    }

    public function getproductSearch(Request $request){
        $recent_products = Product::with('photoproduct')->where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $searchTerm = '%' . $request->search . '%';
        $catId = $request->cat_id;
        $query1 = Product::with('photoproduct')->where('slug', $catId)
                    ->where(function($query) use ($searchTerm) {
                        $query->where('title', 'like', $searchTerm)
                              ->orWhere('slug', 'like', $searchTerm)
                              ->orWhere('description', 'like', $searchTerm)
                              ->orWhere('summary', 'like', $searchTerm)
                              ->orWhere('price', 'like', $searchTerm);
                    })
                    ->orderBy('id', 'DESC');
        $query2 = Product::with('photoproduct')->where(function($query) use ($searchTerm) {
                        $query->where('title', 'like', $searchTerm)
                              ->orWhere('slug', 'like', $searchTerm)
                              ->orWhere('description', 'like', $searchTerm)
                              ->orWhere('summary', 'like', $searchTerm)
                              ->orWhere('price', 'like', $searchTerm);
                    })
                    ->where('slug', '!=', $catId)
                    ->orderBy('id', 'DESC');
        $query3 = Product::with('photoproduct')->where('slug', $catId)
                    ->orderBy('id', 'DESC');
        $products = $query1->union($query2)->union($query3)->distinct()->get();
        return response()->json(['product' => $products, 'recent_products' => $recent_products]);
    }


}
