<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        PropertyType::create([
            'name' => 'منازل للبيع',
            'image' => 'villa_sale.jpeg',
            'property_category_id' => 1,
        ]);

        PropertyType::create([
            'name' => 'شقق سكنية للببيع',
            'image' => 'apartment_sale.jpeg',
            'property_category_id' => 1,
        ]);
        PropertyType::create([
            'name' => 'أراضي للبيع',
            'image' => 'land_sale.jpeg',
            'property_category_id' => 1,
        ]);
        PropertyType::create([
            'name' => 'منازل للأيجار',
            'image' => 'villa_rent.jpeg',
            'property_category_id' => 2,
        ]);

        PropertyType::create([
            'name' => 'شقق سكنية للأيجار',
            'image' => 'apartment_rent.jpeg',
            'property_category_id' => 2,
        ]);
        PropertyType::create([
            'name' => 'أراضي للأيجار',
            'image' => 'land_rent.jpeg',
            'property_category_id' => 2,
        ]);

     
    }
}
