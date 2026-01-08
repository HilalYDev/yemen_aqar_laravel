<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'property_id', 'quantity', 'price','currency'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function property() {
        return $this->belongsTo(Property::class);
    }
}

