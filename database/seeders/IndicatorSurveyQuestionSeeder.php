<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndicatorSurveyQuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('indicator_survey_question')->upsert([
            // =====================================================
            // 🟢 ربط المؤشرات الرقمية بأسئلة موازية
            // =====================================================

            // المؤشر 1 و 2 (إجمالي ونشاط المتطوعين)
            ['indicator_id' => 1, 'question_id' => 1, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 2, 'question_id' => 25, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 3 (نسبة حضور المتطوعين)
            ['indicator_id' => 3, 'question_id' => 18, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 3, 'question_id' => 21, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 4 (إجمالي ساعات المتطوعين)
            ['indicator_id' => 4, 'question_id' => 19, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 4, 'question_id' => 24, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 5 و 6 (نسبة إكمال الحملة ومشاركة المتطوعين)
            ['indicator_id' => 5, 'question_id' => 20, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 5, 'question_id' => 23, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 7 و 8 (النقاط والمكافآت لكل حملة)
            ['indicator_id' => 7, 'question_id' => 22, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 8, 'question_id' => 26, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // =====================================================
            // 🟡 ربط المؤشرات المعنوية (النوعية)
            // =====================================================

            // المؤشر 9: Volunteer Engagement Level (مستوى التفاعل)
            ['indicator_id' => 9, 'question_id' => 2, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 9, 'question_id' => 6, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 9, 'question_id' => 8, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 10: Volunteer Motivation Index (مستوى الدافعية)
            ['indicator_id' => 10, 'question_id' => 1, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 10, 'question_id' => 3, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 10, 'question_id' => 9, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 11: Volunteer Team Cohesion Score (انسجام الفريق)
            ['indicator_id' => 11, 'question_id' => 4, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 11, 'question_id' => 5, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 11, 'question_id' => 7, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 12: Volunteer Awareness Growth Index (نمو الوعي)
            ['indicator_id' => 12, 'question_id' => 11, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 12, 'question_id' => 17, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 13: Logistic & Support Satisfaction Score (الرضا اللوجستي)
            ['indicator_id' => 13, 'question_id' => 12, 'phase' => 'before', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 13, 'question_id' => 13, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 13, 'question_id' => 14, 'phase' => 'during', 'created_at' => now(), 'updated_at' => now()],

            // المؤشر 14: Personal & Professional Impact Score (الأثر الشخصي)
            ['indicator_id' => 14, 'question_id' => 10, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 14, 'question_id' => 15, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],
            ['indicator_id' => 14, 'question_id' => 16, 'phase' => 'after', 'created_at' => now(), 'updated_at' => now()],
        ], ['indicator_id', 'question_id', 'phase'], ['updated_at']);
    }
}
