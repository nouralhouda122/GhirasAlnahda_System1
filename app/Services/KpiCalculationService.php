<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class KpiCalculationService
{
    /**
     * 🟢 Entry Point: حساب KPI كامل
     */
    public function calculate($indicator, $campaignId)
    {
        return match ($indicator->type) {

            'numeric' => $this->calculateNumeric($indicator, $campaignId),

            'qualitative' => $this->calculateQualitative($indicator, $campaignId),

            default => 0,
        };
    }

    // =====================================================
    // 🟢 NUMERIC KPI (Database-based)
    // =====================================================
    private function calculateNumeric($indicator, $campaignId)
    {
        $query = DB::table($indicator->table_name)
            ->where('campaign_id', $campaignId);

        $value = match ($indicator->calculation_type) {

            'count' => $query->count(),

            'sum' => $query->sum($indicator->column_name),

            'avg' => $query->avg($indicator->column_name),

            'percentage' => $this->calculatePercentage($query, $indicator),

            default => 0,
        };

        return $this->normalize($value, $indicator->target_value);
    }

    // =====================================================
    // 🟡 QUALITATIVE KPI (Survey Engine)
    // =====================================================
    private function calculateQualitative($indicator, $campaignId)
    {
        // 🔥 جلب كل الأسئلة المرتبطة بهذا الـ KPI
        $questions = DB::table('indicator_survey_question')
            ->where('indicator_id', $indicator->id)
            ->pluck('survey_question_id');

        if ($questions->isEmpty()) {
            return 0;
        }

        // 🔥 حساب متوسط الإجابات
        $avg = DB::table('survey_answers')
            ->whereIn('survey_question_id', $questions)
            ->where('campaign_id', $campaignId)
            ->avg('answer');

        $max = 5; // scale (1–5)

        return $this->normalize(($avg / $max) * 100, $indicator->target_value);
    }

    // =====================================================
    // 📊 Percentage logic (generic)
    // =====================================================
    private function calculatePercentage($query, $indicator)
    {
        $total = $query->count();

        if ($total === 0) return 0;

        // مثال عام: نجاحات
        $success = (clone $query)
            ->where('status', 'success')
            ->count();

        return ($success / $total) * 100;
    }

    // =====================================================
    // 🎯 Normalize to target (0 - 100)
    // =====================================================
    private function normalize($value, $target = null)
    {
        if (!$target || $target == 0) {
            return round($value, 2);
        }

        return round(min(($value / $target) * 100, 100), 2);
    }

    // =====================================================
    // 🧠 Advanced: KPI with phase breakdown
    // =====================================================
    public function calculateByPhase($indicator, $campaignId)
    {
        $phases = ['before', 'during', 'after'];

        $results = [];

        foreach ($phases as $phase) {

            $questions = DB::table('indicator_survey_question')
                ->where('indicator_id', $indicator->id)
                ->where('phase', $phase)
                ->pluck('survey_question_id');

            $avg = DB::table('survey_answers')
                ->whereIn('survey_question_id', $questions)
                ->where('campaign_id', $campaignId)
                ->avg('answer');

            $results[$phase] = $avg ? round(($avg / 5) * 100, 2) : 0;
        }

        // 🔥 مثال: وزن المراحل (قبل 20% - أثناء 30% - بعد 50%)
        return (
            ($results['before'] * 0.2) +
            ($results['during'] * 0.3) +
            ($results['after'] * 0.5)
        );
    }
}
