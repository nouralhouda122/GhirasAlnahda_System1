<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class indicatorsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $indicators = [
            // =====================================================
            // 🟢 VOLUNTEERS (رقمي - قاعدة بيانات)
            // =====================================================
            [
                'id' => 1, 'name' => 'Total Campaign Volunteers', 'description' => 'إجمالي المتطوعين في الحملة',
                'domain' => 'volunteers', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'count',
                'table_name' => 'campaign_volunteer', 'column_name' => 'volunteer_profile_id', 'survey_config' => null,
                'priority' => 10, 'tags' => json_encode(['volunteers', 'campaign']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'Active Volunteers', 'description' => 'المتطوعين النشطين داخل الحملة',
                'domain' => 'volunteers', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'count',
                'table_name' => 'campaign_volunteer', 'column_name' => 'status', 'survey_config' => null,
                'priority' => 9, 'tags' => json_encode(['volunteers']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 3, 'name' => 'Volunteer Attendance Rate', 'description' => 'نسبة حضور المتطوعين',
                'domain' => 'volunteers', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'avg',
                'table_name' => 'attendances', 'column_name' => 'hours', 'survey_config' => null,
                'priority' => 10, 'tags' => json_encode(['attendance']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 4, 'name' => 'Volunteer Total Hours', 'description' => 'إجمالي ساعات المتطوعين',
                'domain' => 'volunteers', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'sum',
                'table_name' => 'attendances', 'column_name' => 'hours', 'survey_config' => null,
                'priority' => 10, 'tags' => json_encode(['hours']), 'created_at' => $now, 'updated_at' => $now,
            ],

            // =====================================================
            // 🔵 CAMPAIGNS (رقمي - قاعدة بيانات)
            // =====================================================
            [
                'id' => 5, 'name' => 'Campaign Completion Rate', 'description' => 'نسبة إكمال الحملة',
                'domain' => 'campaigns', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'percentage',
                'table_name' => 'campaigns', 'column_name' => 'status', 'survey_config' => null,
                'priority' => 10, 'tags' => json_encode(['campaign']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 6, 'name' => 'Campaign Active Volunteers Ratio', 'description' => 'نسبة مشاركة المتطوعين',
                'domain' => 'campaigns', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'count',
                'table_name' => 'campaign_volunteer', 'column_name' => 'campaign_id', 'survey_config' => null,
                'priority' => 9, 'tags' => json_encode(['campaign']), 'created_at' => $now, 'updated_at' => $now,
            ],

            // =====================================================
            // 🔴 PERFORMANCE (رقمي - قاعدة بيانات)
            // =====================================================
            [
                'id' => 7, 'name' => 'Total Volunteer Points', 'description' => 'إجمالي النقاط المكتسبة',
                'domain' => 'performance', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'sum',
                'table_name' => 'point_transactions', 'column_name' => 'points', 'survey_config' => null,
                'priority' => 10, 'tags' => json_encode(['points']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 8, 'name' => 'Points per Campaign', 'description' => 'النقاط لكل حملة',
                'domain' => 'performance', 'type' => 'numeric', 'data_source' => 'database', 'calculation_type' => 'sum',
                'table_name' => 'point_transactions', 'column_name' => 'points', 'survey_config' => null,
                'priority' => 9, 'tags' => json_encode(['campaign', 'points']), 'created_at' => $now, 'updated_at' => $now,
            ],

            // =====================================================
            // 🟡 QUALITY (معنوي - استبيانات استراتيجية)
            // =====================================================
            [
                'id' => 9, 'name' => 'Volunteer Engagement Level', 'description' => 'مستوى تفاعل المتطوعين داخل الحملة',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['engagement', 'participation']]),
                'priority' => 10, 'tags' => json_encode(['engagement']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 10, 'name' => 'Volunteer Motivation Index', 'description' => 'مستوى الدافعية للاستمرار',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['motivation']]),
                'priority' => 10, 'tags' => json_encode(['motivation']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 11, 'name' => 'Volunteer Team Cohesion Score', 'description' => 'انسجام الفريق التطوعي',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['teamwork', 'cooperation']]),
                'priority' => 10, 'tags' => json_encode(['teamwork']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 12, 'name' => 'Volunteer Awareness Growth Index', 'description' => 'مؤشر نمو الوعي بالقضية والمجتمع المستهدف قبل وبعد الحملة',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['awareness', 'knowledge']]),
                'priority' => 9, 'tags' => json_encode(['awareness', 'education']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 13, 'name' => 'Logistic & Support Satisfaction Score', 'description' => 'مستوى الرضا عن الدعم اللوجستي وتسهيل المهام',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['logistics', 'support']]),
                'priority' => 8, 'tags' => json_encode(['logistics', 'operations']), 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 14, 'name' => 'Personal & Professional Impact Score', 'description' => 'مستوى الأثر الشخصي واكتساب المهارات والتقدير للمتطوع',
                'domain' => 'quality', 'type' => 'qualitative', 'data_source' => 'survey', 'calculation_type' => 'avg',
                'table_name' => null, 'column_name' => null,
                'survey_config' => json_encode(['scale' => 5, 'questions' => ['impact', 'skills', 'appreciation']]),
                'priority' => 10, 'tags' => json_encode(['impact', 'growth']), 'created_at' => $now, 'updated_at' => $now,
            ],
        ];

        DB::table('indicators')->upsert(
            $indicators,
            ['id'],
            [
                'name', 'description', 'domain', 'type', 'data_source',
                'calculation_type', 'table_name', 'column_name',
                'survey_config', 'priority', 'tags', 'updated_at',
            ]
        );
    }
}
