<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class indicatorsSeeder extends Seeder
{
    public function run(): void
    {DB::table('indicators')->insert([

        [
            'name' => 'Total Volunteers',
            'description' => 'عدد المتطوعين الكلي',
            'domain' => 'volunteers',
            'type' => 'numeric',
            'data_source' => 'database',
            'calculation_type' => 'count',
            'table_name' => 'users',
            'column_name' => 'id',
            'operation' => 'count',
            'survey_id' => null,
            'priority' => 10,
        ],

        [
            'name' => 'Active Volunteers',
            'description' => 'عدد المتطوعين النشطين',
            'domain' => 'volunteers',
            'type' => 'numeric',
            'data_source' => 'database',
            'calculation_type' => 'count',
            'table_name' => 'volunteer_profiles',
            'column_name' => 'is_active',
            'operation' => 'filter_true',
            'survey_id' => null,
            'priority' => 10,
        ],

        [
            'name' => 'Volunteer Attendance Rate',
            'description' => 'نسبة حضور المتطوعين',
            'domain' => 'volunteers',
            'type' => 'numeric',
            'data_source' => 'database',
            'calculation_type' => 'percentage',
            'table_name' => 'attendances',
            'column_name' => null,
            'operation' => 'attendance_ratio',
            'survey_id' => null,
            'priority' => 10,
        ],

        [
            'name' => 'Volunteer Satisfaction',
            'description' => 'رضا المتطوعين',
            'domain' => 'quality',
            'type' => 'qualitative',
            'data_source' => 'survey',
            'calculation_type' => 'avg',
            'table_name' => null,
            'column_name' => null,
            'operation' => null,
            'survey_id' => 1,
            'priority' => 10,
        ],

    ]);            }
}
