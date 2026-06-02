<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('user_fcm_tokens', function (Blueprint $table) {
        $table->id();
        // إجبار نوع الحقل ليتطابق مع الـ primary key في جدول users
        $table->unsignedBigInteger('user_id'); 
        $table->string('fcm_token', 500); // تغيير النوع وتحديد الطول لحل مشكلة الـ Unique Index
        $table->string('app_type'); // admin, manager, volunteer
        $table->string('device_type')->nullable(); // android, ios, web
        $table->timestamps();

        // إعداد المفتاح الأجنبي يدوياً وبدقة
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // جعل التوكن فريداً بحد ذاته لأنه لا يمكن لجهازين امتلاك نفس التوكن
        $table->unique('fcm_token', 'fcm_token_unique');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('user_fcm_tokens');
    }
};