<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// migration
    public function up()
    {
        Schema::table('goal_indicator', function (Blueprint $table) {
            $table->float('score', 8, 6)->nullable()->after('indicator_id');
        });
    }

    public function down()
    {
        Schema::table('goal_indicator', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }};
