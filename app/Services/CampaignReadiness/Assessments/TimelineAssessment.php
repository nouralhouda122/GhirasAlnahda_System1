<?php

namespace App\Services\Assessments;

use App\Models\Campaign;
use App\DTOs\AssessmentResult;
use Carbon\Carbon;

/**
 * Class TimelineAssessment
 * مسؤولة عن تقييم جاهزية الجدول الزمني للحملة الإنسانية (Single Responsibility Principle)
 */
class TimelineAssessment
{
    // 1. أوزان الخصم الثابتة (Deduction Weights) لتجنب الـ Magic Numbers
    private const CRITICAL_MISSING_DATE_DEDUCTION = 30; // خصم عند غياب أحد التواريخ الأساسية
    private const INVALID_DATE_ORDER_DEDUCTION    = 40; // خصم كبير عند وجود خطأ منطقي في الترتيب
    private const SHORT_DURATION_DEDUCTION        = 20; // خصم إذا كانت المدة غير كافية للتنفيذ
    private const INSUFFICIENT_PREP_DEDUCTION     = 15; // خصم إذا كان الوقت المتبقي للتحضير حرجاً جداً

    // 2. الحدود الزمنية القياسية (Thresholds)
    private const MINIMUM_SAFE_DURATION_DAYS      = 3;  // الحد الأدنى الآمن لأي حملة ميدانية
    private const RECOMMENDED_DURATION_DAYS       = 7;  // المدة الموصى بها لضمان كفاءة التوزيع
    private const MINIMUM_PREPARATION_DAYS        = 3;  // الحد الأدنى للأيام المطلوبة لتحضير المتطوعين

    /**
     * تقييم الجدول الزمني للحملة وإرجاع كائن النتيجة الموحد
     * * @param Campaign $campaign
     * @return AssessmentResult
     */
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $score = 100; // البداية من الدرجة الكاملة ثم الخصم بناءً على الثغرات
        $strengths = [];
        $weaknesses = [];

        // أ) التحقق من وجود التواريخ الأساسية أولاً في قاعدة البيانات
        if (!$campaign->start_date) {
            $score -= self::CRITICAL_MISSING_DATE_DEDUCTION;
            $weaknesses[] = "تاريخ بدء الحملة غير محدد؛ لا يمكن جدولة المهام الميدانية للفريق.";
        }

        if (!$campaign->end_date) {
            $score -= self::CRITICAL_MISSING_DATE_DEDUCTION;
            $weaknesses[] = "تاريخ انتهاء الحملة غير محدد؛ يعوق حساب الميزانية التشغيلية وفترة الإغلاق المالي.";
        }

        // ب) التحليل الزمني المتقدم في حال توفر التواريخ
        if ($campaign->start_date && $campaign->end_date) {
            // تحويل النصوص إلى كائنات Carbon للتعامل معها برمجياً
            $start = Carbon::parse($campaign->start_date);
            $end = Carbon::parse($campaign->end_date);

            // أولاً: التحقق من منطقية الترتيب الزمني
            if ($start->gte($end)) {
                $score -= self::INVALID_DATE_ORDER_DEDUCTION;
                $weaknesses[] = "خطأ منطقي فادح: تاريخ بدء الحملة يجب أن يكون سابقاً لتاريخ انتهائها.";
            } else {

                // ثانياً: تحليل مدة تنفيذ الحملة (Duration Analysis)
                $duration = $start->diffInDays($end);

                if ($duration < self::MINIMUM_SAFE_DURATION_DAYS) {
                    $score -= self::SHORT_DURATION_DEDUCTION;
                    $weaknesses[] = "مدة الحملة قصيرة جداً (" . $duration . " أيام). قد لا تكفي لتحقيق مستهدفات الـ KPIs ميدانياً.";
                } elseif ($duration >= self::RECOMMENDED_DURATION_DAYS) {
                    $strengths[] = "النطاق الزمني لتنفيذ الحملة ممتاز ومناسب لتوزيع المهام وتجنب احتراق الفريق.";
                }

                // ثالثاً: تحليل وقت التحضير المتاح قبل الانطلاق (Preparation Buffer)
                // معامل false يضمن إرجاع قيمة سالبة إذا كان التاريخ قد مضى
                $daysBeforeStart = now()->diffInDays($start, false);

                if ($daysBeforeStart >= 0 && $daysBeforeStart < self::MINIMUM_PREPARATION_DAYS) {
                    $score -= self::INSUFFICIENT_PREP_DEDUCTION;
                    $weaknesses[] = "الوقت المتبقي لإطلاق الحملة حرج (" . round($daysBeforeStart) . " أيام). خطورة عالية لعدم جاهزية الحشد البشري.";
                } elseif ($daysBeforeStart >= self::MINIMUM_PREPARATION_DAYS) {
                    $strengths[] = "يتوفر وقت تحضيري كافٍ لتهيئة المتطوعين وتجهيز اللوجستيات قبل التفعيل الميداني.";
                }
            }
        }

        // ضمان عدم نزول النتيجة النهائية عن الصفر تحت أي ظرف
        $finalScore = max($score, 0);

        // إرجاع النتيجة ككائن صلب مقيد بالـ DTO
        return new AssessmentResult(
            score: $finalScore,
            status: $this->determineStatus($finalScore),
            strengths: $strengths,
            weaknesses: $weaknesses
        );
    }

    /**
     * تحديد الحالة بناءً على النتيجة الرقمية المحسوبة
     * * @param int $score
     * @return string
     */
    private function determineStatus(int $score): string
    {
        if ($score >= 80) return 'Good';
        if ($score >= 50) return 'Needs Improvement';
        return 'Poor';
    }
}
