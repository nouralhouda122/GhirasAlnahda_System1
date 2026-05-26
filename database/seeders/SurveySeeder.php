<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('surveys')->insert([

            [
                'id' => 1,
                'name' => 'Pre Campaign Survey',
                'type' => 'pre_campaign'
            ],
            [
                'id' => 2,
                'name' => 'During Campaign Survey',
                'type' => 'during_campaign'
            ],
            [
                'id' => 3,
                'name' => 'Post Campaign Survey',
                'type' => 'after_campaign'
            ],

        ]);
    }
}
