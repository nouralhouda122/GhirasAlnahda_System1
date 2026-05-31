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
                'title' => 'Before Campaign Survey',
                'stage' => 'before',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 2,
                'title' => 'During Campaign Survey',
                'stage' => 'during',

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 3,
                'title' => 'After Campaign Survey',
                'stage' => 'after',

                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
