<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndicatorSurveyQuestionSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('indicator_survey_question')->insert([

            // =====================================================
            // Volunteer Motivation Index
            // indicator_id = 10
            // =====================================================

            [
                'indicator_id' => 10,
                'survey_question_id' => 1,
                'phase' => 'before',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'indicator_id' => 10,
                'survey_question_id' => 9,
                'phase' => 'after',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =====================================================
            // Volunteer Team Cohesion Score
            // indicator_id = 11
            // =====================================================

            [
                'indicator_id' => 11,
                'survey_question_id' => 4,
                'phase' => 'during',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'indicator_id' => 11,
                'survey_question_id' => 5,
                'phase' => 'during',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =====================================================
            // Volunteer Engagement Level
            // indicator_id = 9
            // =====================================================

            [
                'indicator_id' => 9,
                'survey_question_id' => 6,
                'phase' => 'during',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'indicator_id' => 9,
                'survey_question_id' => 8,
                'phase' => 'after',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
