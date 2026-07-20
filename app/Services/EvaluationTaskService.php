<?php

namespace App\Services;
use App\Models\EvaluationTask;
use App\Models\SurveyAnswer;
use App\Http\Resources\evaluationTaskResourses;
use App\Repositories\CampaignSurveyRepository;
use App\Repositories\EvaluationTaskRepository;

class EvaluationTaskService
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



public function getMyTasks($userId): array
{
    $tasks = $this->taskRepository->getByEvaluator($userId);

    return [
        'data' => evaluationTaskResourses::collection($tasks),
        'message' => 'Your evaluation tasks retrieved successfully.',
        'code' => 200
    ];
}

// أضيفي هذه التوابع داخل كلاس evaluationTaskService

/**
 * جلب أسئلة الاستبيان المرتبط بمهمة معينة للموظف
 */
public function getTaskSurveyQuestions(int $taskId, int $userId): array
{
    $task = \App\Models\EvaluationTask::with(['survey.surveyQuestions.question'])
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

    // تنسيق خروج الأسئلة بشكل نظيف للتطبيق
    $questions = $task->survey->surveyQuestions->map(function ($sq) {
        return [
            'survey_question_id' => $sq->id, // هذا الـ ID المطلوب لجدول الإجابات
            'order'              => $sq->order,
            'question_text'      => $sq->question?->question_text ?? '', // أو التسمية المعرّفة في جدول الأسئلة عندكِ
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
    return \Illuminate\Support\Facades\DB::transaction(function () use ($taskId, $answersData, $userId) {
        
       $task = \App\Models\EvaluationTask::where([
    ['id', '=', $taskId],
    ['evaluator_id', '=', $userId]
               ])->first();

        if (!$task) {
            return ['data' => '', 'message' => 'Task not found or unauthorized.', 'code' => 404];
        }

        if ($task->status === 'completed') {
            return ['data' => '', 'message' => 'This task has already been completed.', 'code' => 400];
        }

        // إدخال الإجابات في جدول survey_answers دفعة واحدة
        foreach ($answersData as $answerItem) {
            \App\Models\SurveyAnswer::create([
                'survey_id'          => $task->survey_id,
                'survey_question_id' => $answerItem['survey_question_id'],
                'user_id'            => $userId, // معرّف الموظف الذي قام بالتقييم
                'campaign_id'        => $task->campaign_id,
                'answer'             => $answerItem['answer'],
            ]);
        }

   
        return [
            'data'    => true,
            'message' => 'Survey answers submitted and task completed successfully.',
            'code'    => 200
        ];
    });
}
/**
 * تحديث حالة المهمة يدوياً من قبل الموظف (مثال: تحويلها إلى in_progress)
 */
public function updateTaskStatus(int $taskId, string $status, int $userId): array
{
    // التأكد من أن الحالة المرسلة تتبع الحالات المسموحة فقط في جدولكِ
    $allowedStatuses = ['pending', 'in_progress', 'completed'];
    if (!in_array($status, $allowedStatuses)) {
        return ['data' => '', 'message' => 'Invalid status provided.', 'code' => 422];
    }

    $task = \App\Models\EvaluationTask::where([
        ['id', '=', $taskId],
        ['evaluator_id', '=', $userId]
    ])->first();

    if (!$task) {
        return ['data' => '', 'message' => 'Task not found or unauthorized.', 'code' => 404];
    }

    // تحديث الحالة
    $task->update(['status' => $status]);

    return [
        'data'    => $task,
        'message' => "Task status updated to {$status} successfully.",
        'code'    => 200
    ];
}

}
