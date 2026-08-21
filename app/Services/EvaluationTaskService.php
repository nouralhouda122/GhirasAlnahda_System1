<?php

namespace App\Services;

use App\Models\EvaluationTask;
use App\Models\SurveyAnswer;
use App\Http\Resources\evaluationTaskResourses;
use App\Repositories\CampaignSurveyRepository;
use App\Repositories\EvaluationTaskRepository;
use Illuminate\Support\Facades\DB;

class EvaluationTaskService
{
    protected $taskRepository;
    protected $CampaignSurveyRepository;

    public function __construct(EvaluationTaskRepository $taskRepository, CampaignSurveyRepository $CampaignSurveyRepository)
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
            'data'    => evaluationTaskResourses::collection($tasks),
            'message' => 'Evaluation tasks retrieved successfully.',
            'code'    => 200
        ];
    }

    public function getMyTasks($userId): array
    {
        $tasks = $this->taskRepository->getByEvaluator($userId);

        return [
            'data'    => evaluationTaskResourses::collection($tasks),
            'message' => 'Your evaluation tasks retrieved successfully.',
            'code'    => 200
        ];
    }

    /**
     * جلب أسئلة الاستبيان المرتبط بمهمة معينة للموظف
     */
    public function getTaskSurveyQuestions(int $taskId, int $userId): array
    {
        $task = EvaluationTask::with(['survey.surveyQuestions.question'])
            ->where([
                ['id', '=', $taskId],
                ['evaluator_id', '=', $userId]
            ])->first();

        if (!$task) {
            return ['data' => '', 'message' => 'Task not found or unauthorized.', 'code' => 404];
        }

        if (!$task->survey) {
            return ['data' => '', 'message' => 'No active survey associated with this task.', 'code' => 404];
        }

        $questions = $task->survey->surveyQuestions->map(function ($sq) {
            return [
                'survey_question_id' => $sq->id,
                'order'              => $sq->order,
                'question_text'      => $sq->question?->question_text ?? '',
                'type'               => $sq->question?->type ?? 'text',
            ];
        });

        return [
            'data'    => $questions,
            'message' => 'Survey questions retrieved successfully.',
            'code'    => 200
        ];
    }

    /**
     * رفع إجابات الاستبيان ميدانياً وإكمال المهمة
     */
    public function submitTaskAnswers(int $taskId, array $answersData, int $userId): array
    {
        try {
            return DB::transaction(function () use ($taskId, $answersData, $userId) {

                $task = EvaluationTask::where([
                    ['id', '=', $taskId],
                    ['evaluator_id', '=', $userId]
                ])->first();

                if (!$task) {
                    return ['data' => '', 'message' => 'Task not found or unauthorized.', 'code' => 404];
                }

                if ($task->status === 'completed') {
                    return ['data' => '', 'message' => 'This task has already been completed.', 'code' => 400];
                }

                // ✅ إدخال الإجابات باستخدام الأعمدة الموجودة فعلياً في جدول survey_answers فقط
                foreach ($answersData as $answerItem) {
                    SurveyAnswer::create([
                        'survey_question_id' => $answerItem['survey_question_id'],
                        'user_id'            => $userId,
                        'answer'             => is_array($answerItem['answer']) ? json_encode($answerItem['answer']) : $answerItem['answer'],
                    ]);
                }

                // تحديث حالة المهمة إلى completed بعد رفع الإجابات
                $task->update(['status' => 'completed']);

                return [
                    'data'    => true,
                    'message' => 'Survey answers submitted and task completed successfully.',
                    'code'    => 200
                ];
            });
        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to submit survey answers: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }

    /**
     * تحديث حالة المهمة يدوياً من قبل الموظف
     */
    public function updateTaskStatus(int $taskId, string $status, int $userId): array
    {
        $allowedStatuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $allowedStatuses)) {
            return ['data' => '', 'message' => 'Invalid status provided.', 'code' => 422];
        }

        $task = EvaluationTask::where([
            ['id', '=', $taskId],
            ['evaluator_id', '=', $userId]
        ])->first();

        if (!$task) {
            return ['data' => '', 'message' => 'Task not found or unauthorized.', 'code' => 404];
        }

        $task->update(['status' => $status]);

        return [
            'data'    => $task,
            'message' => "Task status updated to {$status} successfully.",
            'code'    => 200
        ];
    }
}