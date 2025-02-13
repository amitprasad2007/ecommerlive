<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
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
            $cart = Cart::where('id', $cart_id)
                ->where('order_id', null)
                ->where('user_id', auth()->user()->id)
                ->where('status', 'new')
                ->first();
            if($cartquantity >0){
                if ($cart) {
                    $cart->quantity = $cartquantity;
                    $cart->amount = $cart->price * $cartquantity; // Update amount based on new quantity
                    $cart->save();
                    $product_id = $cart->product_id;
                    $product = Product::with('photoproduct')->find($product_id);
                    $product->cartquantity = $cartquantity;
                    $product->cart_id = $cart->id;
                    $products[] = $product;
                }
            }else{
                $cart->quantity = 0;
                $cart->amount = 0;
                $cart->status = 'delete';
                $cart->save();
            }
        }
        return response()->json([
            'product' => $products
        ]);
    }
    public function placeorder(Request $request)
    {
        $customeremail = $request->customerDetails['email'];
        $customername = $request->customerDetails['customername'];
        $nameParts = explode(' ', $customername, 2); 
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; 
        $billingAddress = $request->customerDetails['billingAddress'];
        $billingstate = $request->customerDetails['billingstate'];
        $billingzip =  $request->customerDetails['billingzip'];
        $mobile = $request->customerDetails['mobile'];
        $totalcart = $request->products;
        $orderIds = []; // Initialize an array to store order IDs
        foreach($totalcart as $products){
            $slug = $products['slug'];
            $products['quantity'];
            $products['price'];
            $productonly = Product::with('photoproduct')->where('slug', $slug)->first();
            $Order = new Order;
            $Order->user_id = auth()->user()->id;
            $Order->order_number = 'ORD-' . time() . '-' . bin2hex(random_bytes(5));
            $Order->sub_total = $productonly->price;
            $Order->quantity = $products['quantity'];
            $Order->total_amount = $productonly->price * $products['quantity'];
            $Order->status = 'new';
            $Order->payment_method =  $request->pay_method;
            $Order->payment_status = ($request->pay_method=='COD')? "Pending":"Paid";
            $Order->first_name = $firstName;
            $Order->last_name = $lastName;
            $Order->email = $customeremail;
            $Order->phone =$mobile;
            $Order->country ='India';
            $Order->post_code = $billingzip;
            $Order->address1 = $billingAddress;
            $Order->address2 = $billingstate;
            $Order->save();
            $orderIds[] = $Order->id; // Store the order ID in the array
        }
        return response()->json([
            'orderIds' => $orderIds // Return the array of order IDs
        ]);
    }
}
