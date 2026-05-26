<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('indicators', function (Blueprint $table) {

        $table->id();

        $table->string('name');
        $table->text('description')->nullable();

        $table->string('domain');
        $table->string('campaign_type')->nullable();

        $table->enum('type', ['numeric', 'qualitative']);

        $table->enum('data_source', ['database', 'survey', 'manual', 'api']);

        $table->enum('calculation_type', [
            'count',
            'sum',
            'avg',
            'percentage'
        ]);

        $table->string('operation')->nullable();

        $table->string('table_name')->nullable();
        $table->string('column_name')->nullable();

        $table->unsignedBigInteger('survey_id')->nullable();

        $table->decimal('target_value', 10, 2)->nullable();

        $table->decimal('base_weight', 5, 2)->default(1);
        $table->integer('priority')->default(1);

        $table->json('tags')->nullable();

        $table->timestamps();

        $table->index(['domain', 'type']);
    });}
public function down(): void
{
Schema::dropIfExists('indicators');
}
};

