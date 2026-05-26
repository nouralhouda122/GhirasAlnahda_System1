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
        Schema::create('campaign_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->text('goal_text'); // الهدف النصي
            // Step 1 output
            $table->string('domain')->nullable();
            $table->string('intent')->nullable();
            $table->string('type')->nullable();
            $table->integer('target_value')->nullable();
            $table->timestamps();
        });  }
        public function down(): void
    {
        Schema::dropIfExists('campaign_kpis');
    }
};
