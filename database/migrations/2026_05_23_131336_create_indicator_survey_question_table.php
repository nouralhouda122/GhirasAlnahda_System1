<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicator_survey_question', function (Blueprint $table) {

            $table->id();

            $table->foreignId('indicator_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('phase', [
                'before',
                'during',
                'after'
            ]);
            $table->timestamps();
            $table->unique(
                [
                    'indicator_id',
                    'question_id',
                    'phase'
                ],
                'iqp_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'indicator_survey_question'
        );
    }
};
