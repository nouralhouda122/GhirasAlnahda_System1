<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {

            $table->id();

            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('stage', ['before', 'during', 'after']);

            $table->string('title');

            $table->enum('status', ['draft', 'active', 'closed'])
                ->default('draft');

            $table->timestamps();

            $table->unique(['campaign_id', 'stage']);
        });        }
        public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
