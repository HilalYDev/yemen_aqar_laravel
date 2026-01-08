<?php

namespace App\Models;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $fillable = ['name', 'image', 'property_category_id'];

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
