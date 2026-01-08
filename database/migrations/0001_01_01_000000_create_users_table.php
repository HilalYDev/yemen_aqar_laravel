ي<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // معرف المستخدم (مفتاح أساسي)
            $table->string('name'); // اسم المستخدم
            $table->string('phone')->unique(); // رقم الهاتف
            $table->string('verification_code')->nullable(); // كود التحقق (يمكن أن يكون فارغًا)
            $table->boolean('approved')->default(false); // حالة الموافقة على الحساب (افتراضيًا غير موافق)
            $table->boolean('admin_approved')->default(false); // حالة الموافقة من قبل المسؤول (افتراضيًا غير موافق)
            $table->string('token')->nullable(); // التوكن (يمكن أن يكون فارغًا)
            $table->string('type'); // نوع المستخدم (مكتب أو مستخدم عادي)
            $table->string('password'); // كلمة المرور
            $table->date('expiry_date')->nullable(); // تاريخ انتهاء الصلاحية (يمكن أن يكون فارغًا)
            $table->timestamps(); // الحقول الزمنية (created_at و updated_at)
        });
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     // $table->string('phone')->unique();
        //     $table->string('phone'); // رقم الهاتف
        //     $table->string('verification_code')->nullable();// كود التحقق
        //     $table->boolean('approved')->default(false); // الموافقة
        //     $table->string('token')->nullable(); // التوكن
        //     $table->string('type');
        //     $table->string('password');
        //     $table->timestamps();

        // });
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('username');
        //     $table->string('id_number')->unique();
        //     $table->string('phone_number')->unique(); // رقم الهاتف
        //     $table->string('commercial_register');
        //     $table->string('password');
        //     $table->string('verification_code')->nullable(); // كود التحقق
        //     $table->boolean('approve')->default(false); // الموافقة
        //     $table->string('token')->nullable(); // التوكن
        //     $table->string('type');
        //     $table->timestamps();
        // });
    

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
