<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name','description','image', 'price', 'currency','location', 'property_type_id','user_id'];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
