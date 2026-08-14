<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            indicatorsSeeder::class,
            QuestionSeeder::class,
            IndicatorSurveyQuestionSeeder::class,
            ManagerCampanigSedder::class,
            RolePermissionSeeder::class,
            AdminSedder::class, 
        ]);
    }}
