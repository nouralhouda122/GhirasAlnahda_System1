<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            $table->string('volunteer_id_code')->unique()->nullable()->after('user_id');
            $table->string('qr_code_path')->nullable()->after('cv_path');
            $table->date('card_expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            $table->dropColumn(['volunteer_id_code', 'qr_code_path', 'card_expiry_date', 'is_active']);


        });
    }
};
