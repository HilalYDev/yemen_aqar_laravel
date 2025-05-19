<?php

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
        // Schema::create('properties', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->text('description');
        //     $table->string('image');
        //     $table->decimal('price', 10, 2);
        //     $table->string('location');
        //     $table->unsignedBigInteger('property_type_id');
        //     $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
        //     $table->unsignedBigInteger('user_id'); // إضافة حقل المستخدم
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->timestamps();
        // });
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->string('location');
            $table->unsignedBigInteger('property_type_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
