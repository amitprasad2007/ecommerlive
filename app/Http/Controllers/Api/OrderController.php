<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function savecart(Request $request){
        //dd($request)
        $totalcart = $request->cart;
        foreach($totalcart as $cart ){
            $slug = $cart['slug'];
            $quantity = $cart['quantity'];

            $product = Product::where('slug', $slug)->first();
            return response()->json([
                'product' => $product
            ]);
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price-($product->price*$product->discount)/100);
            $cart->quantity = $quantity;
            $cart->amount=$cart->price*$cart->quantity;
            $cart->status = 'new';
            $cart->save();
            $products[]=$product;
        }
        
      
    }
}
