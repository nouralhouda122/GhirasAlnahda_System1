<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('questions')->insert([
            // =====================================================
            // BEFORE CAMPAIGN (الأسئلة قبل الحملة)
            // =====================================================
            [
                'id' => 1,
                'question_text' => 'هل تشعر بالحماس للمشاركة في الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 2,
                'question_text' => 'هل أهداف الحملة واضحة بالنسبة لك؟',
                'type' => 'rating', 'scale' => 5, 'order' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 3,
                'question_text' => 'هل تشعر أنك مستعد للمشاركة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 3,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 11,
                'question_text' => 'ما مدى معرفتك السابقة بالقضية أو المجتمع المستهدف في هذه الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 4,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 12,
                'question_text' => 'هل ترى أن التدريب أو التوجيه الأولي الذي تلقيته كان كافياً للبدء؟',
                'type' => 'rating', 'scale' => 5, 'order' => 5,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 18,
                'question_text' => 'هل تتوقع التزاماً كاملاً بجدول الحضور والمواعيد المقترحة للحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 6,
                'created_at' => now(), 'updated_at' => now(),
            ],

            // =====================================================
            // DURING CAMPAIGN (الأسئلة أثناء الحملة)
            // =====================================================
            [
                'id' => 4,
                'question_text' => 'ما مدى رضاك عن التعاون بين أعضاء الفريق؟',
                'type' => 'rating', 'scale' => 5, 'order' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 5,
                'question_text' => 'هل تشعر أن التواصل داخل الفريق فعال؟',
                'type' => 'rating', 'scale' => 5, 'order' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 6,
                'question_text' => 'هل تشعر أن قائد الفريق يدعمك؟',
                'type' => 'rating', 'scale' => 5, 'order' => 3,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 7,
                'question_text' => 'هل بيئة العمل مريحة نفسيًا؟',
                'type' => 'rating', 'scale' => 5, 'order' => 4,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 13,
                'question_text' => 'ما مدى توفر الأدوات والموارد اللوجستية اللازمة لأداء مهامك اليومية؟',
                'type' => 'rating', 'scale' => 5, 'order' => 5,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 14,
                'question_text' => 'هل يتم التعامل مع المشكلات والعقبات الميدانية بسرعة وكفاءة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 6,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 19,
                'question_text' => 'هل تجد مرونة وسلاسة في توزيع ساعات العمل والمهام الميدانية؟',
                'type' => 'rating', 'scale' => 5, 'order' => 7,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 20,
                'question_text' => 'ما مدى التزام فريق العمل ككل بالخطة الزمنية المقررة للحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 8,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 21,
                'question_text' => 'هل آلية تسجيل الحضور والغياب المتبعة حالياً سهلة ومنصفة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 9,
                'created_at' => now(), 'updated_at' => now(),
            ],

            // =====================================================
            // AFTER CAMPAIGN (الأسئلة بعد الحملة)
            // =====================================================
            [
                'id' => 8,
                'question_text' => 'ما مدى رضاك العام عن الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 9,
                'question_text' => 'هل ترغب بالمشاركة في حملات مستقبلية؟',
                'type' => 'rating', 'scale' => 5, 'order' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 10,
                'question_text' => 'هل تعتقد أن الحملة حققت أثرًا إيجابيًا؟',
                'type' => 'rating', 'scale' => 5, 'order' => 3,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 15,
                'question_text' => 'ما مدى التطور أو المهارات الجديدة التي اكتسبتها نتيجة مشاركتك؟',
                'type' => 'rating', 'scale' => 5, 'order' => 4,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 16,
                'question_text' => 'هل شعرت بالتقدير المعنوي من إدارة الحملة بعد انتهائها؟',
                'type' => 'rating', 'scale' => 5, 'order' => 5,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 17,
                'question_text' => 'ما مدى وعيك واهتمامك الحالي بالقضية مقارنة بما قبل الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 6,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 22,
                'question_text' => 'هل نظام النقاط والمكافآت المكتسبة كان محفزاً وعادلاً بالنسبة لك؟',
                'type' => 'rating', 'scale' => 5, 'order' => 7,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 23,
                'question_text' => 'هل تشعر أن دورك كمتطوع ساهم بشكل مباشر في رفع نسبة إنجاز وإكمال الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 8,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 24,
                'question_text' => 'هل كانت ساعات التطوع الإجمالية مناسبة ولم تسبب لك عبئاً أو إرهاقاً؟',
                'type' => 'rating', 'scale' => 5, 'order' => 9,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 25,
                'question_text' => 'هل تنصح أصدقائك بالانضمام كمتطوعين في النسخ القادمة من هذه الحملة؟',
                'type' => 'rating', 'scale' => 5, 'order' => 10,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 26,
                'question_text' => 'ما مدى رضاك عن التقارير النهائية أو التغذية الراجعة التي استلمتها حول أدائك؟',
                'type' => 'rating', 'scale' => 5, 'order' => 11,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
