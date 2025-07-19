<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AddressUser;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userlogin(Request $request){
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            $user =  User::create([
                'mobile' => $request->mobile,
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make($request->password)
            ]);
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json(['token' => $token]);
            }else{
                return response()->json(['message' => 'Something went wrong try again'], 401);
            }
        } else {
            if ($request->password) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('authToken')->plainTextToken;
                    return response()->json(['token' => $token]);
                } else {
                    return response()->json(['message' => 'Invalid email or password'], 401);
                }
            }
            return response()->json(['message' => 'please send password'], 200);
        }
    }
    
    public function userauth(){
        $user = Auth::user();
        $address = $user->default_address; 
        return response()->json(['user' => $user,'address'=>$address]);
    }

    public function saveshippinginfo(Request $request){
        $user = Auth::user();
        $address = AddressUser::create([
            'user_id' => $user->id,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country??'India',
            'address_type' => $request->address_type??'home',
            'is_default' => $request->is_default?:false,
        ]);
        $user->email = $request->email;
        $user->name = $request->firstName." ".$request->lastName;
        $user->save();
        $user->address_users()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        $addressArray = $address->toArray();
        $addressArray['email'] = $request->email;
        return response()->json(['message' => 'Shipping info saved successfully', 'address' => $addressArray], 200);
    }

    public function getshippinginfo(){
        $user = Auth::user();
        $address = $user->address_users()->first();
        if (!$address) {
            return response()->json([
                'message' => 'No shipping address found',
                'address' => null
            ], 200);
        }
        $addressArray = $address->toArray();
        $addressArray['email'] = $user->email;
        return response()->json(['address' => $addressArray], 200);
    }

    public function updateprofile(Request $request){
        $user = Auth::user();
        $address = AddressUser::find($request->addressid);
        $address->address=$request->address;
        $address->address2=$request->address2;
        $address->city=$request->city;
        $address->country=$request->country;
        $address->postal_code=$request->postal_code;
        $address->state=$request->state;
        $address->firstName=$request->firstName;
        $address->lastName=$request->lastName;
        $address->mobile=$request->mobile;
        $address->save();        
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->name = $request->firstName." ".$request->lastName;
        $user->save();
        return response()->json(['message' => 'Shipping info saved successfully'], 200);
    }

    public function changepassword(Request $request){
        $currentPassword = $request->currentPassword;
        $newPassword = $request->newPassword;
        $user = Auth::user();
        if (Hash::check($currentPassword, $user->password)){
            $user->password = Hash::make($newPassword);
            $user->save();            
        }
        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}
