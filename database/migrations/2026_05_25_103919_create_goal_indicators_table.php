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
        Schema::create('goal_indicator', function (Blueprint $table) {

            $table->id();

            $table->foreignId('campaign_kpi_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('indicator_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->float('score')->nullable();

            $table->timestamps();

            $table->unique(['campaign_kpi_id', 'indicator_id']);
        });            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_indicators');
    }
};
