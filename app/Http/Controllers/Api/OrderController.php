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

            // Check for existing cart entry with null order_id, same product_id, and status 'new'
            $existingCart = Cart::where('product_id', $product->id)
                ->where('order_id', null)
                ->where('status', 'new')
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($existingCart) {
                // Return product with cartquantity and existing cart id
                $product->cartquantity = $existingCart->quantity;
                $product->cart_id = $existingCart->id;
                $products[] = $product;
                continue; // Skip creating a new cart entry
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
        if (!isset($request->cart) || !is_array($request->cart) || count($request->cart) < 1) {
            return response()->json(['error' => 'Invalid cart data'], 400);
        }

        $products = []; // Initialize products array
        $totalcart = $request->cart;
        foreach($totalcart as $cartv ){
            $cart_id =  $cartv['cart_id'];
            $cartquantity = $cartv['cartquantity'];

               // Find the existing cart entry
            $cart = Cart::where('id', $cart_id)
                ->where('order_id', null)
                ->where('user_id', auth()->user()->id)
                ->where('status', 'new')
                ->first();
                        return response()->json([
                'product' => $cart
                        ]);
            if($cartquantity >0){
                if ($cart) {
                    // Update the cart quantity
                    $cart->quantity = $cartquantity;
                    $cart->amount = $cart->price * $cart->quantity; // Update amount based on new quantity
                    $cart->save();

                    // Retrieve all products in the user's cart
                    $userCarts = Cart::where('user_id', auth()->user()->id)
                        ->where('order_id', null)
                        ->where('status', 'new')
                        ->with('product') // Assuming you want to load product details
                        ->get();
                    //  dd($userCarts);
                    foreach ($userCarts as $userCart) {
                        $product_id = $userCart->product->id;
                        // dd($product_id);
                        $product = Product::with('photoproduct')->find($product_id);
                        $product->cartquantity = $userCart->quantity;
                        $product->cart_id = $userCart->id;
                        $products[] = $product;
                    }
                }
            }else{
                $cart->quantity = $cartquantity;
                $cart->amount = 0;
                // Update amount based on new quantity
                $cart->save();
            }


        }
        return response()->json([
            'product' => $products
        ]);
    }
}
