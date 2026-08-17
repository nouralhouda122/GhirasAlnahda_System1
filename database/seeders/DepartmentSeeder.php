<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Campaign Department',
                'description' => 'Handles campaigns management',
            ],
            [
                'name' => 'Evaluation Department',
                'description' => 'Handles evaluation tasks and surveys',
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                ['description' => $department['description']]
            );
        }
    }
}
