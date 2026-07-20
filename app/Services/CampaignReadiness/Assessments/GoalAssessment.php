<?php

namespace App\Services\Assessments;

use App\Models\Campaign;
use App\DTOs\AssessmentResult;

class GoalAssessment
{
    // 1. تعريف أوزان الخصم الثابتة (Deduction Weights) لمنع الـ Magic Numbers
    private const NO_KPIS_DEDUCTION             = 40; // خصم قاسي جداً لأن حملة بلا أهداف عمياء
    private const INSUFFICIENT_KPIS_DEDUCTION   = 15; // أقل من هدفين غير كافٍ للحملات الإنسانية
    private const MISSING_TARGET_VALUE_WEIGHT    = 15; // خصم لكل هدف لا يحتوي على قيمة رقمية مستهدفة
    private const LOW_GOAL_WEIGHT_WARNING       = 5;  // خصم إذا كان وزن الهدف الإستراتيجي ضعيفاً جداً

    // 2. الحدود القياسية (Thresholds)
    private const RECOMMENDED_MIN_KPIS = 2;
    private const MINIMUM_SAFE_WEIGHT  = 0.5;

    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $score = 100;
        $strengths = [];
        $weaknesses = [];

        // جلب مؤشرات الأداء المرتبطة بالحملة من قاعدة البيانات
        $kpis = $campaign->kpis;
        $kpiCount = $kpis->count();

        // أ) التحقق من وجود أهداف كلياً
        if ($kpiCount === 0) {
            $score -= self::NO_KPIS_DEDUCTION;
            $weaknesses[] = "خطأ فادح: لم يتم تسجيل أي مؤشرات أداء (KPIs) أو أهداف قابلة للقياس لهذه الحملة.";

            return new AssessmentResult(
                score: max($score, 0),
                status: 'Poor',
                strengths: $strengths,
                weaknesses: $weaknesses
            );
        }

        // ب) التحقق من كفاية عدد الأهداف بناءً على نوع الحملة
        if ($kpiCount < self::RECOMMENDED_MIN_KPIS) {
            $score -= self::INSUFFICIENT_KPIS_DEDUCTION;
            $weaknesses[] = "تحذير: عدد الأهداف المدرجة منخفض جداً. يفضل تفكيك الهدف الرئيسي إلى هدفين فرعيين أو أكثر لضمان دقة التنفيذ.";
        } else {
            $strengths[] = "الحملة تحتوي على هيكل أهداف متعددة يغطي جوانب التنفيذ المختلفة.";
        }

        // ج) الفحص التفصيلي لكل مؤشر أداء (Deep Loop Analysis)
        $fullyMeasurableCount = 0;

        foreach ($kpis as $kpi) {
            // التحقق من جودة القياس الرقمي (Target Value)
            if (is_null($kpi->target_value) || $kpi->target_value <= 0) {
                $score -= self::MISSING_TARGET_VALUE_WEIGHT;
                $weaknesses[] = "الهدف [{$kpi->goal_text}] صياغته إنشائية ويفتقد لقيمة رقمية مستهدفة (Target Value).";
            } else {
                $fullyMeasurableCount++;
            }

            // التحقق من الوزن الاستراتيجي للمؤشر (KPI Weight)
            if ($kpi->weight < self::MINIMUM_SAFE_WEIGHT) {
                $score -= self::LOW_GOAL_WEIGHT_WARNING;
                $weaknesses[] = "الهدف [{$kpi->goal_text}] مُعطى وزناً تأثيرياً ضعيفاً جداً في النظام ({$kpi->weight}).";
            }
        }

        // د) إضافة نقاط القوة بناءً على النتيجة الإجمالية للفحص
        if ($fullyMeasurableCount === $kpiCount) {
            $strengths[] = "ممتاز! جميع أهداف الحملة مصممة بذكاء وقابلة للقياس الرقمي بنسبة 100%.";
        }

        // ضمان بقاء النتيجة في النطاق الإيجابي
        $finalScore = max($score, 0);

        return new AssessmentResult(
            score: $finalScore,
            status: $this->determineStatus($finalScore),
            strengths: $strengths,
            weaknesses: $weaknesses
        );
    }

    private function determineStatus(int $score): string
    {
        if ($score >= 80) return 'Good';
        if ($score >= 50) return 'Needs Improvement';
        return 'Poor';
    }
}
