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
    Schema::create('team_requests', function (Blueprint $table) {
        $table->id();

        // الحملة المطلوبة
        $table->foreignId('campaign_id')
            ->constrained()
            ->cascadeOnDelete();

        // منشئ الطلب
        $table->foreignId('creator_volunteer_profile_id')
            ->constrained('volunteer_profiles')
            ->cascadeOnDelete();

        // حالة الطلب
        $table->enum('status', [
            'pending',
            'completed',
            'cancelled',
            'expired'
        ])->default('pending');
        $table->unsignedTinyInteger('required_acceptance_percentage')->default(80);

        // انتهاء صلاحية الدعوات
        $table->timestamp('expires_at');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_requests');
    }
};
