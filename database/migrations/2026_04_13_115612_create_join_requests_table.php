<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('current_address');
            $table->string('cv_path');
            $table->enum('preferred_sector', ['relief', 'educational', 'medical', 'administrative']);
            $table->enum('preferred_field', ['food_distribution', 'psychological_support', 'teaching', 'data_entry', 'media_marketing', 'logistics', 'first_aid']);
            $table->integer('weekly_hours_capacity');
            $table->string('message_title')->nullable();
            $table->text('message_content')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_requests');
    }
};
