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
            'image' => 'properties_for_sale.jpeg',
                        // 'image' => 'properties_for_rent.jpeg',

        ]);

        PropertyCategory::create([
            'name' => 'عقارات للأيجار',
            'image' => 'properties_for_rent.jpeg',
        ]);
    
    

      
    }
}
