<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Property;
use App\Models\OfficeDetail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasFactory, Notifiable;


    protected $fillable = [
        'name', 'phone', 'verification_code', 'approved', 'admin_approved', 'token', 'type', 'password', 'expiry_date',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];



    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function carts() {
    return $this->hasMany(Cart::class);
}
   public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
