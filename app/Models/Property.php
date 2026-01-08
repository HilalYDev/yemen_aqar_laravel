<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name','description','image',        'ownership_image', 
 'price', 'currency','location', 'property_type_id','user_id','is_sold'];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // إضافة هذه العلاقات
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
}
