<?php

namespace App\Services;

use App\Http\Resources\evaluationTaskResourses;
use App\Repositories\CampaignSurveyRepository;
use App\Repositories\EvaluationTaskRepository;

class evaluationTaskService
{
    protected $taskRepository;
    protected  $CampaignSurveyRepository;

    public function __construct(EvaluationTaskRepository $taskRepository,CampaignSurveyRepository $CampaignSurveyRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->CampaignSurveyRepository = $CampaignSurveyRepository;

    }

    public function createEvaluationTask(array $data): array
    {
        $survey = $this->CampaignSurveyRepository->getById((int)$data['survey_id']);

        if (!$survey) {
            return ['data' => '', 'message' => 'The selected survey does not exist.', 'code' => 404];
        }

        // 2. التحقق من تبعية الاستبيان للحملة
        if ((int)$survey->campaign_id !== (int)$data['campaign_id']) {
            return [
                'data'    => '',
                'message' => 'The selected survey does not belong to this campaign.',
                'code'    => 422
            ];
        }

        // 3. التحقق من تطابق مرحلة الاستبيان مع مرحلة المهمة
        if ($survey->stage !== $data['stage']) {
            return [
                'data'    => '',
                'message' => "The survey stage ({$survey->stage}) does not match the task stage ({$data['stage']}).",
                'code'    => 422
            ];
        }

        // 4. التحقق من أن الاستبيان معتمد ونشط
        if ($survey->status !== 'active') {
            return [
                'data'    => '',
                'message' => 'Cannot assign a task with a draft or closed survey. The survey must be active.',
                'code'    => 422
            ];
        }

        // 5. الحفظ المعتمد على الـ Repository
        try {
            $taskId = $this->taskRepository->create($data);
            $insertedTask = $this->taskRepository->getById($taskId);

            return [
                'data'    => $insertedTask,
                'message' => 'Evaluation task created and assigned successfully.',
                'code'    => 200
            ];
        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to create task: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }

    /**
     * معالجة قائمة المهام لإعادتها للكونترولر
     */
    public function getAllEvaluationTasks(): array
    {
        $tasks = $this->taskRepository->getAll();

        return [
            'data'    =>  evaluationTaskResourses::collection ($tasks),
            'message' => 'Evaluation tasks retrieved successfully.',
            'code'    => 200
        ];
    }
}
