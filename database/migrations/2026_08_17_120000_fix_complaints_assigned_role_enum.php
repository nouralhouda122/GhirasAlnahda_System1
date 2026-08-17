<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /*
    |--------------------------------------------------------------------------
    | مواءمة enum العمود assigned_role مع أسماء الأدوار الفعلية
    |--------------------------------------------------------------------------
    | كان الـ enum يحوي 'Campaign Manager' و 'Evaluation Manager' وهما اسمان
    | غير موجودين في جدول roles، بينما الكود يرسل 'Manager' و 'Volunteer Manager'.
    | النتيجة: خطأ 1265 Data truncated عند إنشاء شكوى من المستوى 1 أو 2.
    */
    public function up(): void
    {
        // 1. توسيع الـ enum مؤقتاً ليقبل الاسمين القديم والجديد معاً
        DB::statement("
            ALTER TABLE `complaints` MODIFY `assigned_role`
            ENUM(
                'Campaign Manager','Evaluation Manager','Super Admin',
                'Manager','Volunteer Manager'
            ) NOT NULL
        ");

        // 2. ترحيل الصفوف القديمة إلى أسماء الأدوار الحقيقية
        DB::table('complaints')->where('assigned_role', 'Campaign Manager')
            ->update(['assigned_role' => 'Manager']);
        DB::table('complaints')->where('assigned_role', 'Evaluation Manager')
            ->update(['assigned_role' => 'Volunteer Manager']);

        // 3. تضييق الـ enum على الأسماء الصحيحة فقط
        DB::statement("
            ALTER TABLE `complaints` MODIFY `assigned_role`
            ENUM('Manager','Volunteer Manager','Super Admin') NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `complaints` MODIFY `assigned_role`
            ENUM(
                'Campaign Manager','Evaluation Manager','Super Admin',
                'Manager','Volunteer Manager'
            ) NOT NULL
        ");

        DB::table('complaints')->where('assigned_role', 'Manager')
            ->update(['assigned_role' => 'Campaign Manager']);
        DB::table('complaints')->where('assigned_role', 'Volunteer Manager')
            ->update(['assigned_role' => 'Evaluation Manager']);

        DB::statement("
            ALTER TABLE `complaints` MODIFY `assigned_role`
            ENUM('Campaign Manager','Evaluation Manager','Super Admin') NOT NULL
        ");
    }
};
