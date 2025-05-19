<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $propertyTypes = PropertyType::all();
        $userIds = User::where('type', 'office')->pluck('id');
        $currencies = ["دولار", "ريال سعودي", "ريال يمني"]; // قائمة العملات
    
        foreach ($propertyTypes as $type) {
            for ($i = 0; $i < 5; $i++) {
                $randomCurrency = $currencies[array_rand($currencies)]; // اختيار عملة عشوائية
    
                Property::create([
                    'name' => 'عقار ' . ($i + 1) . ' - ' . $type->name,
                    'description' => 'وصف لهذا العقار من نوع ' . $type->name,
                    'image' => 'house3.jpg',
                    'price' => rand(50000, 500000),
                    'currency' => $randomCurrency, // استخدام العملة العشوائية
                    'location' => 'مدينة عشوائية ' . rand(1, 10),
                    'property_type_id' => $type->id,
                    'user_id' => "2",
                ]);
            }
        }
    
        ////===================================================================================================
        // $users = User::all(); // جلب جميع المستخدمين
        // $propertyTypes = PropertyType::all(); // جلب جميع أنواع العقارات
        // $defaultImage = 'house3.jpg'; // الصورة الموحدة لجميع العقارات

        // foreach ($propertyTypes as $type) {
        //     for ($i = 1; $i <= 100; $i++) {
        //         Property::create([
        //             'name' => 'عقار ' . $i . ' - ' . $type->name,
        //             'description' => 'وصف للعقار رقم ' . $i . ' من نوع ' . $type->name,
        //             'image' => $defaultImage, // تطبيق نفس الصورة
        //             'price' => rand(50000, 500000),
        //             'currency' => 'USD',
        //             'location' => 'مدينة ' . rand(1, 50),
        //             'property_type_id' => $type->id,
        //             'user_id' => $users->random()->id, // اختيار مستخدم عشوائي
        //         ]);
        //     }
        // }

        // // توزيع 10 عقارات لكل مستخدم
        // foreach ($users as $user) {
        //     for ($i = 1; $i <= 10; $i++) {
        //         Property::create([
        //             'name' => 'عقار المستخدم ' . $user->id . ' - ' . $i,
        //             'description' => 'عقار خاص بالمستخدم رقم ' . $user->id,
        //             'image' => $defaultImage, // تطبيق نفس الصورة
        //             'price' => rand(60000, 800000),
        //             'currency' => 'USD',
        //             'location' => 'حي رقم ' . rand(1, 20),
        //             'property_type_id' => $propertyTypes->random()->id, // اختيار نوع عشوائي
        //             'user_id' => $user->id,
        //         ]);
        //     }
        // }
                ////===================================================================================================

        // Property::create([

         
        //     'name' => 'شقة فاخرة في وسط المدينة',
        //     'description' => 'شقة فاخرة في وسط المدينة مكون من ثلاث غرف كبيره  وصالة معيشة ومطبخ وحمام ',
        //     'image' => 'house1.jpg',
        //     'price' => 500000,
        //     'currency' => "ريال يمني",
        //     'location' => 'الرياض',
        //     'property_type_id' => 1,
        //     'user_id' => '2',

        // ]);

        // Property::create([
      
        //     'name' => 'فيلا فاخرة مع مسبح',
        //     'description' => 'فيلا فاخرة مع مسبح مكون من ثلاث غرف كبيره  وصالة',
        //           'image' => 'house2.jpg',
        //     'price' => 1500000,
        //     'currency' => "ريال سعودي",

        //     'location' => 'جدة',
        //     'property_type_id' => 1,
        //     'user_id' => '2',


            
        // ]);
        // Property::create([
      
        //     'name' => "test",
        //     'description' => "test",
        //           'image' => 'house3.jpg',
        //     'price' => 1200,
        //     'currency' => "دولار",

        //     'location' => 'test',
        //     'property_type_id' => 1,
        //     'user_id' => '2',


            
        // ]);

    //     // إنشاء 20 عقار وهمي
    //     // Property::factory()->count(20)->create();
    }
}