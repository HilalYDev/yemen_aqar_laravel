<?php

namespace App\Models;

use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
      protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'property_id',
        'quantity',
    ];

    // علاقة المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة العقار
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

}
