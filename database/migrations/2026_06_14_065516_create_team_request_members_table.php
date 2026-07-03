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
    Schema::create('team_request_members', function (Blueprint $table) {
        $table->id();

        $table->foreignId('team_request_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('volunteer_profile_id')
            ->constrained('volunteer_profiles')
            ->cascadeOnDelete();

        $table->enum('status', [
            'pending',
            'accepted',
            'rejected'
        ])->default('pending');

        $table->timestamp('responded_at')->nullable();

        $table->timestamps();

        $table->unique([
            'team_request_id',
            'volunteer_profile_id'
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_request_members');
    }
};
