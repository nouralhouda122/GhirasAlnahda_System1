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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Complaint Main Information
            |--------------------------------------------------------------------------
            */
            $table->string('title');
            $table->text('description');
            $table->boolean('is_anonymous')->default(false);
            $table->string('attachment_path')->nullable();


            $table->enum('sensitivity_level', [
                'level_1',
                'level_2',
                'level_3'
            ])->default('level_1');


          $table->enum('assigned_role', [
             'Campaign Manager',
             'Evaluation Manager',
              'Super Admin']);

            /*
            |--------------------------------------------------------------------------
            | Assignment System
            |--------------------------------------------------------------------------
            | الموظف المحدد الذي استلم الشكوى وبدأ بالعمل عليها لمنع التضارب
            */
            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'in_progress',
                'resolved',
                'rejected'
            ])->default('pending');


            $table->text('admin_reply')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
