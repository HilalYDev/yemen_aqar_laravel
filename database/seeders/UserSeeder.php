<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $users = [
            ['name' => 'هلال مستخدم', 'phone' => '773355465', 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'user','admin_approved' => '1', 'password' => Hash::make('Helal@735')],
            ['name' => 'هلال ادمن', 'phone' => '773355466', 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office','expiry_date' => '2026-03-18','admin_approved' => '1', 'password' => Hash::make('Helal@735')],
            // ['name' => 'هلال ادمن2', 'phone' => '773355467', 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office', 'password' => Hash::make('Helal@735')],
            // ['name' => 'محمد', 'phone' => '09' . rand(100000000, 999999999), 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office', 'password' => Hash::make('Helal@735')],
            // ['name' => 'سعيد', 'phone' => '09' . rand(100000000, 999999999), 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office', 'password' => Hash::make('Helal@735')],
            // ['name' => 'خالد', 'phone' => '09' . rand(100000000, 999999999), 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office', 'password' => Hash::make('Helal@735')],
            // ['name' => 'علي','phone' => '09' . rand(100000000, 999999999), 'verification_code' => rand(10000, 99999),'approved' => true,   'token' => Str::random(60),  'type' => 'office', 'password' => Hash::make('Helal@735')],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
        // for ($i = 1; $i <= 5; $i++) {
        //     User::create([
        //         'name' => 'User ' . $i,
        //         'phone' => '09' . rand(100000000, 999999999), // رقم هاتف عشوائي
        //         'verification_code' => rand(10000, 99999), // كود تحقق عشوائي
        //         'approved' => true,
        //         'token' => Str::random(60), // توليد Token عشوائي
        //         'type' => 'office', // نوع المستخدم مكتب
        //         'password' => Hash::make('Helal@735'), // كلمة مرور مشفرة
        //     ]);
        // }
        // User::create([
        //     'name' => 'هلال الوزان',
        //     'phone' => '773355465',
        //     'verification_code' => '11111',
        //     'approved' => false,
        //     'type' => "user",
        //     'password' => Hash::make('Helal@735'),

        // ]);
        // User::create([
        //     'name' => 'Admin1',
        //     'phone' => '777777771',
        //     'verification_code' => '22222',
        //     'approved' => false,
        //     'type' => "office",
        //     'password' => Hash::make('Helal@735'),
        // ]);
        // User::create([
        //     'name' => 'Admin2',
        //     'phone' => '777777772',
        //     'verification_code' => '33333',
        //     'approved' => false,
        //     'type' => "office",
        //     'password' => Hash::make('Helal@735'),
        // ]);
        // User::create([
        //     'name' => 'Admin3',
        //     'phone' => '777777773',
        //     'verification_code' => '44444',
        //     'approved' => false,
        //     'type' => "office",
        //     'password' => Hash::make('Helal@735'),
        // ]);


        // إنشاء 10 مستخدمين وهميين
        // User::factory()->count(10)->create();
    }
}
