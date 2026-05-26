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
            // 🟡 PRE CAMPAIGN (قبل الحملة)
            // =====================================================

            [
                'survey_id' => 1,
                'question_text' => 'How clear are the campaign objectives?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 1,
                'question_text' => 'How confident do you feel about your role?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 1,
                'question_text' => 'How well do you understand the campaign plan?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 1,
                'question_text' => 'How prepared do you feel before starting?',
                'type' => 'rating',
                'scale' => 5
            ],

            // =====================================================
            // 🟡 DURING CAMPAIGN (أثناء الحملة)
            // =====================================================

            [
                'survey_id' => 2,
                'question_text' => 'How smooth is the coordination between team members?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 2,
                'question_text' => 'How effective is communication during the campaign?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 2,
                'question_text' => 'How well are tasks being organized?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 2,
                'question_text' => 'How satisfied are you with leadership support?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 2,
                'question_text' => 'How comfortable is the working environment?',
                'type' => 'rating',
                'scale' => 5
            ],

            // =====================================================
            // 🟡 POST CAMPAIGN (بعد الحملة)
            // =====================================================

            [
                'survey_id' => 3,
                'question_text' => 'Overall, how satisfied are you with the campaign?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'Did the campaign achieve its goals effectively?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'Would you participate again in future campaigns?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'How impactful was the campaign on the community?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'How would you rate the quality of execution?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'How satisfied are you with team performance?',
                'type' => 'rating',
                'scale' => 5
            ],
            [
                'survey_id' => 3,
                'question_text' => 'What improvements would you suggest? (optional)',
                'type' => 'text',
                'scale' => null
            ],
        ]);
    }
}
