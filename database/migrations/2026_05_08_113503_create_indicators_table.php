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

        // =========================
        // 🧠 CORE DEFINITION
        // =========================
        $table->string('name');
        $table->text('description')->nullable();

        $table->string('domain')->index(); // volunteers, donations, etc
        $table->string('campaign_type')->nullable()->index();

        $table->enum('type', ['numeric', 'qualitative'])->index();

        // =========================
        // 📊 DATA SOURCE MODEL
        // =========================
        $table->enum('data_source', ['database', 'survey', 'manual', 'api'])->index();

        // =========================
        // ⚙️ CALCULATION ENGINE
        // =========================
        $table->enum('calculation_type', [
            'count',
            'sum',
            'avg',
            'percentage'
        ])->nullable();

        // For DB-based indicators only
        $table->string('table_name')->nullable();
        $table->string('column_name')->nullable();

        // =========================
        // 🧠 SURVEY LINKING (CLEANER)
        // =========================
        // بدل survey_id الأفضل تخليه mapping فقط عبر pivot
        // لذلك نحذفه أو نتركه optional reference فقط
        $table->json('survey_config')->nullable();
        /*
            مثال:
            {
                "scale": 5,
                "aggregation": "avg",
                "phase_weight": {
                    "before": 0.2,
                    "during": 0.3,
                    "after": 0.5
                }
            }
        */

        // =========================
        // 🎯 BUSINESS LOGIC
        // =========================
        $table->decimal('target_value', 10, 2)->nullable();

        $table->decimal('base_weight', 5, 2)->default(1);
        $table->unsignedInteger('priority')->default(1);

        // =========================
        // 🏷️ SEMANTIC LAYER (AI FRIENDLY)
        // =========================
        $table->json('tags')->nullable();
        /*
            ["volunteers", "engagement", "growth"]
        */

        // =========================
        // 📈 SCORING / AI TUNING
        // =========================
        $table->decimal('default_score_weight', 5, 2)->default(1);

        // optional: threshold for success
        $table->decimal('success_threshold', 5, 2)->nullable();
        $table->decimal('warning_threshold', 5, 2)->nullable();

        // =========================
        // 🔍 STATUS & CONTROL
        // =========================
        $table->boolean('is_active')->default(true);
        $table->boolean('is_system')->default(false); // system-defined vs user-defined

        // =========================
        // ⚡ PERFORMANCE
        // =========================
        $table->timestamps();

        $table->index(['domain', 'type', 'data_source']);
        $table->index(['priority', 'is_active']);
        });}
public function down(): void
{
Schema::dropIfExists('indicators');
}
};

