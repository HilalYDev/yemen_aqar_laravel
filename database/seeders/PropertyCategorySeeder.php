<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        PropertyCategory::create([
            'name' => 'عقارات للبيع',
            'image' => 'house1.jpg',
        ]);

        PropertyCategory::create([
            'name' => 'عقارات للأيجار',
            'image' => 'house1.jpg',
        ]);
        PropertyCategory::create([
            'name' => 'المكاتب العقارية',
            'image' => 'house1.jpg',
        ]);
    

      
    }
}
