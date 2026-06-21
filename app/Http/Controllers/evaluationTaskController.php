<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\evaluationTaskRequest;
use App\Services\EvaluationTaskService;
use Illuminate\Http\JsonResponse;
use Exception; // 👈 قمنا بتعديل الـ namespace هنا ليكون الـ Exception العام لـ PHP

class evaluationTaskController extends Controller
{
    protected $taskService;

    public function __construct(EvaluationTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * إضافة مهمة تقييم جديدة لحملة
     */
    public function store(evaluationTaskRequest $request): JsonResponse
    {
        try {
            $data = $this->taskService->createEvaluationTask($request->all());

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            // 👈 تعديل نص الرسالة ليناسب سياق مهام التقييم
            return ResponseHelper::Error([], 'حدث خطأ أثناء إضافة مهمة التقييم: ' . $e->getMessage(), 500);
        }
    }

    /**
     * عرض جميع مهام التقييم
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->taskService->getAllEvaluationTasks();

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            // 👈 تعديل نص الرسالة ليناسب سياق مهام التقييم
            return ResponseHelper::Error([], 'حدث خطأ أثناء جلب مهام التقييم: ' . $e->getMessage(), 500);
        }
    }

public function myTasks(): JsonResponse
{
    try {
        $data = $this->taskService->getMyTasks(auth()->id());

        return ResponseHelper::Success(
            $data['data'],
            $data['message'],
            200
        );

    } catch (\Exception $e) {

        return ResponseHelper::Error(
            [],
            'Failed to retrieve your tasks: '.$e->getMessage(),
            500
        );
    }
}


// أضيفي هذه الدوال داخل كلاس evaluationTaskController

/**
 * واجهة جلب أسئلة الاستبيان الخاص بمهمة محددة
 */
public function getQuestions($id): JsonResponse
{
    try {
        $data = $this->taskService->getTaskSurveyQuestions($id, auth()->id());

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], 200);
        }

        return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
    } catch (Exception $e) {
        return ResponseHelper::Error([], 'Failed to retrieve survey questions: ' . $e->getMessage(), 500);
    }
}

/**
 * واجهة استقبال الإجابات وإغلاق المهمة
 */
public function submitAnswers(\Illuminate\Http\Request $request, $id): JsonResponse
{
    // عمل Validation سريع للإجابات للتأكد من بنية الـ Array القادمة
    $request->validate([
        'answers'                      => 'required|array',
        'answers.*.survey_question_id' => 'required|exists:survey_questions,id',
        'answers.*.answer'             => 'required|string',
    ]);

    try {
        $data = $this->taskService->submitTaskAnswers($id, $request->answers, auth()->id());

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], 200);
        }

        return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
    } catch (Exception $e) {
        return ResponseHelper::Error([], 'Failed to submit survey answers: ' . $e->getMessage(), 500);
    }
}

/**
 * واجهة تغيير حالة المهمة من قبل الموظف
 */
public function updateStatus(\Illuminate\Http\Request $request, $id): JsonResponse
{
    $request->validate([
        'status' => 'required|string|in:pending,in_progress,completed',
    ]);

    try {
        $data = $this->taskService->updateTaskStatus($id, $request->status, auth()->id());

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], 200);
        }

        return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
    } catch (Exception $e) {
        return ResponseHelper::Error([], 'Failed to update task status: ' . $e->getMessage(), 500);
    }
}

}
