<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | إنشاء الأدمن
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'يمن عقار - أدمن',
            'phone' => '773355465',
            'verification_code' => rand(10000, 99999),
            'approved' => true,
            'admin_approved' => true,
            'token' => Str::random(60),
            'type' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | إنشاء مالك عقار رئيسي
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'مالك عقار رئيسي',
            'phone' => '777777777',
            'verification_code' => rand(10000, 99999),
            'approved' => true,
            'admin_approved' => true,
            'token' => Str::random(60),
            'type' => 'property_owner',
            'password' => Hash::make('Pass@735'),
            'expiry_date' => Carbon::now()->addYear(), // اشتراك سنة
        ]);
        User::create([
            'name' => 'مالك عقار رئيسي',
            'phone' => '777777770',
            'verification_code' => rand(10000, 99999),
            'approved' => true,
            'admin_approved' => true,
            'token' => Str::random(60),
            'type' => 'property_owner',
            'password' => Hash::make('Pass@735'),
            'expiry_date' => Carbon::now()->addYear(), // اشتراك سنة
        ]);

             User::create([
                'name' => "User",
                'phone' => '777777778',
                'verification_code' => rand(10000, 99999),
                'approved' => true,
                'admin_approved' => true,
                'token' => Str::random(60),
                'type' => 'user',
                'password' => Hash::make('Pass@735'),
            ]);

        // /*
        // |--------------------------------------------------------------------------
        // | إنشاء 5 مستخدمين عاديين
        // |--------------------------------------------------------------------------
        // */
        // for ($i = 1; $i <= 5; $i++) {
        //     User::create([
        //         'name' => "User $i",
        //         'phone' => '09111111' . $i,
        //         'verification_code' => rand(10000, 99999),
        //         'approved' => true,
        //         'admin_approved' => true,
        //         'token' => Str::random(60),
        //         'type' => 'user',
        //         'password' => Hash::make('password'),
        //     ]);
        // }

        // /*
        // |--------------------------------------------------------------------------
        // | إنشاء 5 ملاك عقارات باشتراك فعال
        // |--------------------------------------------------------------------------
        // */
        // for ($i = 1; $i <= 5; $i++) {
        //     User::create([
        //         'name' => "Property Owner $i",
        //         'phone' => '09222222' . $i,
        //         'verification_code' => rand(10000, 99999),
        //         'approved' => true,
        //         'admin_approved' => true,
        //         'token' => Str::random(60),
        //         'type' => 'property_owner',
        //         'password' => Hash::make('password'),
        //         'expiry_date' => Carbon::now()->addMonths(6),
        //     ]);
        // }

        // /*
        // |--------------------------------------------------------------------------
        // | إنشاء 3 ملاك عقارات منتهية اشتراكاتهم
        // |--------------------------------------------------------------------------
        // */
        // for ($i = 6; $i <= 8; $i++) {
        //     User::create([
        //         'name' => "Expired Property Owner $i",
        //         'phone' => '09333333' . $i,
        //         'verification_code' => rand(10000, 99999),
        //         'approved' => true,
        //         'admin_approved' => false,
        //         'token' => Str::random(60),
        //         'type' => 'property_owner',
        //         'password' => Hash::make('password'),
        //         'expiry_date' => Carbon::now()->subDays(10),
        //     ]);
        // }
    }
}
