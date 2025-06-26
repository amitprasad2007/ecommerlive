<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'sub_total',
        'quantity',
        'tax',
        'shippingcost',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_details',
        'status',
        'shipping_id',
        'coupon',
        'transaction_id'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the address associated with the order.
     */
    public function address()
    {
        return $this->belongsTo(AddressUser::class, 'address_id');
    }

    /**
     * Get the cart items for the order.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(){
        return $this->hasOne(Payment::class,'order_id','order_number');
    }

    public static function countActiveOrder(){
        $data=Order::count();
        if($data){
            return $data;
        }
        return 0;
    }
}
