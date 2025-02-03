<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function savecart(Request $request){
        // Validate the request
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array',
            'cart.*.slug' => 'required|string|exists:products,slug',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $totalcart = $request->cart;
        $products = []; // Initialize products array

        foreach($totalcart as $cart ){
            $slug = $cart['slug'];
            $quantity = $cart['quantity'];

            $product = Product::with('photoproduct')->where('slug', $slug)->first();
            if (!$product) {
                continue; // Skip if product not found
            }

            $product->cartquantity = $quantity;
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = $quantity;
            $cart->amount = $cart->price * $cart->quantity;
            $cart->status = 'new';
            $cart->save();
            $product->cart_id = $cart->id;
            $products[] = $product;            
        }

        return response()->json([
            'product' => $products
        ]);
    }

    public function updatecart(Request $request){ 
        // Ensure the cart is an array and has at least one item
        if (isset($request->cart[0]) && is_array($request->cart[0])) {
            return response()->json([
                'product' => $request->cart // Accessing 'title' as an array key
            ]);
        }

        return response()->json(['error' => 'Invalid cart data'], 400); // Handle error case
    }
}
