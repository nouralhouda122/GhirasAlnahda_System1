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
        Schema::create('indicator_feedback', function ($table) {

            $table->id();

            $table->foreignId('indicator_id')
                ->constrained('indicators')
                ->cascadeOnDelete();

            $table->boolean('is_accepted')->default(true);

            $table->integer('score')->default(1);

            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_feedback');
    }
};
