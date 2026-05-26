<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_volunteer', function (Blueprint $table) {
            $table->id();
// الربط بجدول بروفايل المتطوعين وليس المستخدمين
            $table->foreignId('volunteer_profile_id')->constrained('volunteer_profiles')->onDelete('cascade');

            // ربط الحملة
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');

            // حالة المتطوع داخل الحملة
            $table->enum('status', ['approved', 'left', 'removed'])->default('approved');

            // منع التكرار بناءً على بروفايل المتطوع والحملة
            $table->unique(['volunteer_profile_id', 'campaign_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_volunteer');
    }
};
