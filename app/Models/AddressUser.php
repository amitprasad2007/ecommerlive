<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class AddressUser extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'firstName','lastName', 'mobile', 'address', 'address2', 'city', 'state', 'postal_code', 'country', 'address_type', 'is_default'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
