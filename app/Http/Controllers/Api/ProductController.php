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

    public function getproductSearch(Request $request){
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $products=Product::orwhere('title','like','%'.$request->search.'%')
                    ->orwhere('slug','like','%'.$request->search.'%')
                    ->orwhere('description','like','%'.$request->search.'%')
                    ->orwhere('summary','like','%'.$request->search.'%')
                    ->orwhere('price','like','%'.$request->search.'%')
                    ->orderBy('id','DESC');
            if(!empty($request->cat_id) || $request->cat_id !='' ){
                $products->where('cat_id',$request->cat_id);
            }                    
            $products->paginate('9');
            return  response()->json(['product' => $product,'recent_products'=> $recent_products]);
    }


}
