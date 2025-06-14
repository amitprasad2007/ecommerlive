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
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array',
            'cart.*.slug' => 'required|string|exists:products,slug',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $totalcart = $request->cart;

        foreach($totalcart as $cart ){
            $slug = $cart['slug'];
            $quantity = $cart['quantity'];

            $product = Product::with('photoproduct')->where('slug', $slug)->first();
            if (!$product) {
                continue;
            }

            $existingCart = Cart::where('product_id', $product->id)
                ->where('status', 'new')
                ->where('order_id', null)
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($existingCart) {
                $existingCart->quantity = $quantity;
                $existingCart->price = ($product->price - ($product->price * $product->discount) / 100);
                $existingCart->amount = $existingCart->price * $quantity;
                $existingCart->save();
            }else{
                $cart = new Cart;
                $cart->user_id = auth()->user()->id;
                $cart->product_id = $product->id;
                $cart->price = ($product->price - ($product->price * $product->discount) / 100);
                $cart->quantity = $quantity;
                $cart->amount = $cart->price * $cart->quantity;
                $cart->status = 'new';
                $cart->save();
            }
        }

        $formattedCart = $this->cartdata();

        return response()->json($formattedCart);
    }

    public function updatecart(Request $request){
        //dd($request->all());
        if (!isset($request->cart) || !is_array($request->cart) || count($request->cart) < 1) {
            return response()->json(['error' => 'Invalid cart data'], 400);
        }
        $totalcart = $request->cart;
        foreach($totalcart as $cartv ){
            $cart_id =  $cartv['cart_id'];
            $cartquantity = $cartv['quantity'];
            $cart = Cart::where('id', $cart_id)
                ->where('order_id', null)
                ->where('user_id', auth()->user()->id)
                ->where('status', 'new')
                ->first();
            if($cartquantity >0){
                $cart->quantity = $cartquantity;
                $cart->amount = $cart->price * $cartquantity;
                $cart->save();
            }else{
                $cart->quantity = 0;
                $cart->amount = 0;
                $cart->status = 'delete';
                $cart->save();
            }
        }
        $formattedCart = $this->cartdata();
        //dd($formattedCart);
        return response()->json($formattedCart);
    }

    public function placeorder(Request $request){
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
        $orderIds = [];
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
            $Order->payment_method =  $products['pay_method'];
            $Order->payment_status = ($products['pay_method'] == 'cod') ? "unpaid" : "paid";
            $Order->first_name = $firstName;
            $Order->last_name = $lastName;
            $Order->email = $customeremail;
            $Order->product_id =  $productonly->id;
            $Order->phone =$mobile;
            $Order->country ='India';
            $Order->post_code = $billingzip;
            $Order->address1 = $billingAddress;
            $Order->address2 = $billingstate;
            $Order->save();
            $cart = Cart::find($products['cart_id']);
            if ($cart) {
                $cart->order_id = $Order->id;
                $cart->status='progress';
                $cart->save();
            }
            $orderIds[] = $Order->order_number;
        }
        return response()->json([
            'orderIds' => $orderIds
        ]);
    }

    public function getcartdata(){
        $formattedCart = $this->cartdata();
        return response()->json($formattedCart);
    }

    public function removecart(Request $request){
        if (!isset($request->cart) || !is_array($request->cart) || count($request->cart) < 1) {
            return response()->json(['error' => 'Invalid cart data'], 400);
        }
        $totalcart = $request->cart;
        $cart_id = $totalcart[0]['cart_id'];
        $cart = Cart::find($cart_id);
        if($cart){
            $cart->quantity = 0;
            $cart->amount = 0;
            $cart->status = 'delete';
            $cart->save();
        }
        return response()->json(['message' => 'Cart removed successfully']);
    }

    private function cartdata(){
        $cartItems = Cart::with('product')
            ->where('status', 'new')
            ->where('order_id', null)
            ->where('user_id', auth()->user()->id)
            ->get();

        $formattedCart = $cartItems->map(function($item) {
            $photo = $item->product->photoproduct->first();
            return [
                'slug' => $item->product->slug,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'amount' => $item->amount,
                'cart_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->title,
                'product_image' => $photo ? asset('storage/products/photos/thumbnails/'.$photo->photo_path) : null,
                'product_price' => $item->product->price,
                'product_discount' => $item->product->discount,
                'product_price_after_discount' => $item->product->price - ($item->product->price * $item->product->discount) / 100,
            ];
        });
        return $formattedCart;
    }
}
