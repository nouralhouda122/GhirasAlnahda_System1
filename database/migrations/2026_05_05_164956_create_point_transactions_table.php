<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('volunteer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('campaigns')
                ->onDelete('cascade');

            $table->integer('points');

            $table->string('type');

            $table->string('reason')->nullable();

            $table->text('description')->nullable();
            $table->foreignId('awarded_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
