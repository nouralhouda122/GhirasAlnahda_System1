<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EvaluationTaskRepository
{
    /**
     * حفظ مهمة تقييم جديدة في قاعدة البيانات
     */
    public function create(array $data): int
    {
        return DB::table('evaluation_tasks')->insertGetId([
            'campaign_id'  => $data['campaign_id'],
            'evaluator_id' => $data['evaluator_id'],
            'survey_id'    => $data['survey_id'],
            'stage'        => $data['stage'],
            'title'        => $data['title'],
            'status'       => 'pending',
            'due_date'     => $data['due_date'] ?? null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * جلب قائمة كافة مهام التقييم مع بيانات الربط الأساسية
     */
    public function getAll()
    {
        return DB::table('evaluation_tasks')
            ->join('campaigns', 'evaluation_tasks.campaign_id', '=', 'campaigns.id')
            ->join('users', 'evaluation_tasks.evaluator_id', '=', 'users.id')
            ->join('surveys', 'evaluation_tasks.survey_id', '=', 'surveys.id')
            ->select(
                'evaluation_tasks.id as task_id',
                'evaluation_tasks.title as task_title',
                'evaluation_tasks.stage as task_stage',
                'evaluation_tasks.status as task_status',
                'evaluation_tasks.due_date',
                'campaigns.id as campaign_id',
                'campaigns.title as campaign_title', // بافتراض اسم حقل اسم الحملة title
                'users.id as evaluator_id',
                'users.name as evaluator_name',     // بافتراض اسم الموظف name
                'surveys.id as survey_id',
                'surveys.title as survey_title'
            )
            ->orderBy('evaluation_tasks.created_at', 'desc')
            ->get();
    }

    /**
     * جلب مهمة محددة بعد إنشائها للتأكد من تفاصيلها
     */
    public function getById(int $id)
    {
        return DB::table('evaluation_tasks')->where('id', $id)->first();
    }
   public function getByEvaluator(int $userId)
{
    return DB::table('evaluation_tasks')
        ->join('campaigns', 'evaluation_tasks.campaign_id', '=', 'campaigns.id')
        ->join('users', 'evaluation_tasks.evaluator_id', '=', 'users.id')
        ->join('surveys', 'evaluation_tasks.survey_id', '=', 'surveys.id')
        ->select(
            'evaluation_tasks.id as task_id',
            'evaluation_tasks.title as task_title',
            'evaluation_tasks.stage as task_stage',
            'evaluation_tasks.status as task_status',
            'evaluation_tasks.due_date',
            'evaluation_tasks.created_at',

            'campaigns.id as campaign_id',
            'campaigns.title as campaign_title',

            'users.id as evaluator_id',
            'users.name as evaluator_name',

            'surveys.id as survey_id',
            'surveys.title as survey_title'
        )
        ->where('evaluation_tasks.evaluator_id', $userId)
        ->orderBy('evaluation_tasks.created_at', 'desc')
        ->get();
}
}
