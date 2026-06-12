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
        Schema::create('campaign_evaluations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('score', 5, 2);

            $table->enum('phase', [
                'before',
                'during',
                'after',
                'monthly'
            ]);

            $table->string('evaluation_type')
                ->default('snapshot');

            $table->text('notes')
                ->nullable();

            $table->timestamp('evaluated_at');

            $table->timestamps();
        });}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_evaluations');
    }
};
