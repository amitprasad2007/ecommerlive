<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

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
        $recent_products = Product::with('photoproduct')
                            ->where('status', 'active')
                            ->orderBy('id', 'DESC')
                            ->limit(4)
                            ->get();

        $searchTerm = $request->search ? '%' . $request->search . '%' : null;
        $catslug = $request->cat_id;
        $catId = null;

        if ($catslug) {
            $category = Category::where('slug', $catslug)->first();
            $catId = $category ? $category->id : null;
        }

        $query1 = Product::query();
        if ($catId) {
            $query1->where('cat_id', $catId);
        }
        if ($searchTerm) {
            $query1->where(function($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('slug', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('summary', 'like', $searchTerm)
                      ->orWhere('price', 'like', $searchTerm);
            });
        }
        $query1->with('photoproduct')->orderBy('id', 'DESC');

        $query2 = Product::query();
        if ($searchTerm) {
            $query2->where(function($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('slug', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('summary', 'like', $searchTerm)
                      ->orWhere('price', 'like', $searchTerm);
            });
        }
        if ($catId) {
            $query2->where('cat_id', '!=', $catId);
        }
        $query2->with('photoproduct')->orderBy('id', 'DESC');

        $query3 = Product::query();
        if ($catId) {
            $query3->where('cat_id', $catId);
        }
        $query3->with('photoproduct')->orderBy('id', 'DESC');

        $products = $query1->union($query2)->union($query3)->distinct()->paginate(9);

        return response()->json(['product' => $products, 'recent_products' => $recent_products]);
    }


}
