<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations.
     */
    public function up(): void
    {
        Schema::create('goal_indicators', function (Blueprint $table) {

            $table->id();
            $table->decimal(
                'target_value',
                10,
                2
            )->nullable();

            $table->foreignId('campaign_kpi_id')

                ->constrained('campaign_kpis')
                ->cascadeOnDelete();
            $table->foreignId('indicator_id')
                ->constrained('indicators')
                ->cascadeOnDelete();
            $table->decimal('score', 5, 2)
                ->default(0);

            $table->integer('ranking')
                ->nullable();

            $table->text('match_reason')
                ->nullable();


            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->foreignId('approved_by_monitor')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_indicators');
    }
};
