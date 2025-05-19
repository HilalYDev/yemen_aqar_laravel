<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OfficeDetail;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficeDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $officeUsers = User::where('type', 'office')->get();

        foreach ($officeUsers as $user) {
            OfficeDetail::create([
                'user_id' => $user->id,
                'office_name' => 'مكتب ' . $user->name,
                'identity_number' => rand(100000, 999999),
                'image' => 'office.jpg',
                'office_address' => 'عنوان ' . $user->name,
                'office_phone' => $user->phone,
            ]);
        }
    
        // $users = User::take(5)->pluck('id'); // الحصول على 100 مستخدم
    
        // foreach ($users as $userId) {
        //     OfficeDetail::create([
        //         'user_id' => $userId,
        //         'office_name' => 'Office ' . $userId,
        //         'identity_number' => rand(1000000000, 9999999999), // رقم هوية عشوائي
        //         'commercial_register_image' => 'commercial_register_' . $userId . '.jpg', // صورة تجارية وهمية
        //         'office_address' => 'Address for office ' . $userId,
        //         'office_phone' => '09' . rand(100000000, 999999999),
        //     ]);
        // }
        // OfficeDetail::create([
        //     'user_id' => 2, // افترض أن المستخدم الأول موجود
        //     'office_name'=> 'مكتب العقارات الأول',
        //                 'identity_number' => '123456789',
        //     'commercial_register_image' => 'house1.jpg',
        //     'office_address' =>'صنعاء - سعوان',
        //     'office_phone' => '773366465',
        // ]);
        // OfficeDetail::create([
        //     'user_id' => 3, // افترض أن المستخدم الأول موجود
        //     'office_name'=> 'مكتب العقارات الثاني',
        //                 'identity_number' => '123456789',
        //     'commercial_register_image' => 'house2.jpg',
        //     'office_address' =>'صنعاء - حده',
        //     'office_phone' => '773366465',
        // ]);
        // OfficeDetail::create([
        //     'user_id' => 4, // افترض أن المستخدم الأول موجود
        //     'office_name'=> 'مكتب العقارات الثالث',
        //                 'identity_number' => '123456789',
        //     'commercial_register_image' => 'house3.jpg',
        //     'office_address' =>'صنعاء - سعوان',
        //     'office_phone' => '773366465',
        // ]);
 
    }
}
