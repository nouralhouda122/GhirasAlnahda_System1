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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->integer('duration_hours');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('cost',10,2)->default(0);
            $table->unsignedInteger('required_points')->default(0);
            $table->foreignId('instructor_id')->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
