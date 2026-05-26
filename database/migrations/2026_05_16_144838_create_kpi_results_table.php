<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::create('kpi_results', function (Blueprint $table) {

            $table->id();

            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('campaign_kpi_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('indicator_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->float('value')->nullable();
            $table->float('achievement')->nullable();

            $table->timestamp('calculated_at');

            $table->timestamps();

            $table->index(['campaign_id', 'campaign_kpi_id']);
        });}
        public function down()
    {
        Schema::dropIfExists('kpi_results');
    }    };


