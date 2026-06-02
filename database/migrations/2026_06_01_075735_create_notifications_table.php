<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // المستخدم المستهدف بالإشعار
            $table->string('type');                // نوع الإشعار (مثلاً: campaign_completed, new_complaint)
            $table->string('title');               // عنوان الإشعار
            $table->json('data');                  // نص الإشعار وتفاصيل إضافية (مثل تفاصيل الحملة أو الشكوى)
            $table->timestamp('read_at')->nullable(); // وقت قراءة الإشعار (null تعني غير مقروء)
            $table->timestamps();

            // ربط المفتاح الأجنبي مع جدول المستخدمين
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};