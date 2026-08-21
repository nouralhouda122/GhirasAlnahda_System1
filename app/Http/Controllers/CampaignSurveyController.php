<?php
namespace App\Http\Controllers;

    use App\Http\Requests\addQuestionToSurveyRequest;
    use App\Http\Requests\SurveyStageRequest;
    use App\Http\Requests\updateQuestionToSurveyRequest;
    use App\Models\Survey;
    use App\Services\CampaignSurveyService;
    use App\Helpers\ResponseHelper;
    use Illuminate\Http\JsonResponse;
    use Exception;

class CampaignSurveyController extends Controller
{
    protected CampaignSurveyService $campaignSurveyService;

    public function __construct(CampaignSurveyService $campaignSurveyService)
    {
        $this->campaignSurveyService = $campaignSurveyService;
    }
    public function results($surveyId): JsonResponse
    {
        $survey = Survey::with([
            'campaign',
            'surveyQuestions.question',
            'surveyQuestions.answers',
        ])->find($surveyId);

        if (!$survey) {
            return response()->json([
                'status' => 0,
                'message' => 'Survey not found.',
                'data' => null,
            ], 404);
        }

        $questions = $survey->surveyQuestions
            ->sortBy('order')
            ->map(function ($surveyQuestion) {

                $answers = $surveyQuestion->answers;

                $totalResponses = $answers->count();

                $answerResults = $answers
                    ->groupBy('answer')
                    ->map(function ($items, $answer) use ($totalResponses) {

                        $count = $items->count();

                        return [
                            'answer' => $answer,
                            'count' => $count,
                            'percentage' => $totalResponses > 0
                                ? round(
                                    ($count / $totalResponses) * 100,
                                    2
                                )
                                : 0,
                        ];
                    })
                    ->values();

                return [
                    'survey_question_id' => $surveyQuestion->id,

                    'question_id' => $surveyQuestion->question_id,

                    'question' => $surveyQuestion->question?->question_text,

                    'order' => $surveyQuestion->order,

                    'total_responses' => $totalResponses,

                    'answers' => $answerResults,
                ];            })
            ->values();

        return response()->json([
            'status' => 1,

            'data' => [
                'survey' => [
                    'id' => $survey->id,
                    'title' => $survey->title,
                    'campaign_id' => $survey->campaign_id,
                    'campaign_title' => $survey->campaign?->title,
                ],

                'total_responses' => $survey->surveyQuestions
                    ->sum(function ($surveyQuestion) {
                        return $surveyQuestion->answers->count();
                    }),

                'questions' => $questions,
            ],
        ]);
    }    /**
     * عرض استبيان محدد حسب الـ ID
     */
    public function showBySurveyId($surveyId): JsonResponse
    {
        try {
            $data = $this->campaignSurveyService->showById((int)$surveyId);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (\Exception $e) {
            return ResponseHelper::Error([], 'حدث خطأ أثناء جلب بيانات الاستبيان: ' . $e->getMessage(), 500);
        }
    }
    public function show($campaign_id, SurveyStageRequest $request): JsonResponse
    {
        try {
            // تمرير المعاملات بنجاح إلى السيرفيس
            $data = $this->campaignSurveyService->show((int)$campaign_id, $request->stage);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            // 👈 تم تعديل نص الرسالة هنا لتصبح معبرة عن جلب الاستبيان
            return ResponseHelper::Error([], 'حدث خطأ أثناء جلب بيانات الاستبيان: ' . $e->getMessage(), 500);
        }
    }
//اضافة سؤال لمؤشر واستبيان
    public function addQuestionToSurvey($surveyId, addQuestionToSurveyRequest $request): JsonResponse
    {
        try {
            $data = $this->campaignSurveyService->addQuestionToSurvey($surveyId, $request);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            return ResponseHelper::Error([], 'حدث خطأ أثناء إضافة السؤال: ' . $e->getMessage(), 500);
        }
    }

    /**
     * تعديل سؤال في استبيان
     */
    public function updateQuestionToSurvey($surveyId, $questionId, updateQuestionToSurveyRequest $request): JsonResponse
    {
        try {
            $data = $this->campaignSurveyService->updateQuestionToSurvey($surveyId, $questionId, $request);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            return ResponseHelper::Error([], 'حدث خطأ أثناء تعديل السؤال: ' . $e->getMessage(), 500);
        }
    }

    /**
     * حذف سؤال من استبيان
     */
    public function deleteQuestionToSurvey($surveyId, $questionId): JsonResponse
    {
        try {
            $data = $this->campaignSurveyService->deleteQuestionFromSurvey($surveyId, $questionId);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['data'], $data['message'], 200);
            }

            return ResponseHelper::Error($data['data'] ?? [], $data['message'], $data['code']);
        } catch (Exception $e) {
            return ResponseHelper::Error([], 'حدث خطأ أثناء حذف السؤال: ' . $e->getMessage(), 500);
        }
    }
    //اعتماد استبيان
        /**
         * اعتماد ونشر الاستبيان للعامة
         */
        public function approveSurvey($surveyId)
        {
            $response = $this->campaignSurveyService->approveSurvey((int)$surveyId);

            if ($response['code'] !== 200) {
                return response()->json([
                    'status'  => 0,
                    'data'    => $response['data'],
                    'message' => $response['message']
                ], $response['code']);
            }

            return response()->json([
                'status'  => 1,
                'data'    => $response['data'],
                'message' => $response['message']
            ], 200);
        }}
