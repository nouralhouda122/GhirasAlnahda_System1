<?php

namespace App\Services;

use App\Repositories\CampaignSurveyRepository;
use App\Repositories\CampaingRepository;
use App\Repositories\SurveyQuestionRepository;
use App\Repositories\IndicatorRepository;
use App\Http\Requests\addQuestionToSurveyRequest;
use App\Http\Resources\CampaignSurveyResource;
use Illuminate\Support\Facades\DB;
use Exception;

class CampaignSurveyService
{
    protected CampaignSurveyRepository $CampaignSurveyRepository;
    protected SurveyQuestionRepository $SurveyQuestionRepository;
    protected IndicatorRepository $indicatorRepository;

    public function __construct(
        CampaignSurveyRepository $campaignSurveyRepository,
        SurveyQuestionRepository $surveyQuestionRepository,
        IndicatorRepository $indicatorRepository,
        CampaingRepository $campaingRepository,
    ) {
        $this->CampaignSurveyRepository = $campaignSurveyRepository;
        $this->SurveyQuestionRepository = $surveyQuestionRepository;
        $this->indicatorRepository      = $indicatorRepository;
        $this->campaingRepository      = $campaingRepository
;

    }



    public function addQuestionToSurvey($surveyId, addQuestionToSurveyRequest $request): array
    {
        $survey = $this->CampaignSurveyRepository->getById($surveyId);
        if (!$survey) {
            return [
                'data'    => '',
                'message' => 'Survey not found',
                'code'    => 404
            ];
        }

        $isIndicatorLinked = $this->indicatorRepository->isLinkedToCampaign(
            $survey->campaign_id,
            $request->indicator_id
        );

        if (!$isIndicatorLinked) {
            return [
                'data'    => '',
                'message' => 'The selected indicator is not linked to this campaign strategic goals.',
                'code' => 422
            ];
        }

        try {
            $result = DB::transaction(function () use ($surveyId, $request) {

                $question = $this->SurveyQuestionRepository->create([
                    'question_text' => $request->question_text,
                    'type'          => $request->type,
                    'scale'         => $request->type === 'rating' ? $request->scale : null,
                    'order'         => $request->order ?? 1,
                ]);

                $this->SurveyQuestionRepository->attachToSurvey(
                    $surveyId,
                    $question->id,
                    $request->order ?? 1
                );

                $this->SurveyQuestionRepository->attachToIndicator(
                    $request->indicator_id,
                    $question->id,
                    $request->phase
                );

                return $this->CampaignSurveyRepository->getById($surveyId);
            });

            return [
                'data'    => new CampaignSurveyResource($result),
                'message' => 'Question added successfully to the survey and mapped to its indicator.',
                'code'    => 200
            ];

        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to add question: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }
    /**
     * تحديث بيانات سؤال وتعديل ارتباطاته بشكل ديناميكي آمن
     */
    public function updateQuestionToSurvey(int $surveyId, int $questionId, $request): array
    {
        // 1. التحقق من وجود الاستبيان أولاً
        $survey = $this->CampaignSurveyRepository->getById($surveyId);
        if (!$survey) {
            return ['data' => '', 'message' => 'Survey not found', 'code' => 404];
        }
// يُوضع بعد فحص الـ if (!$survey) مباشرة
        if ($survey->status === 'active' || $survey->status === 'closed') {
            return [
                'data'    => '',
                'message' => 'Cannot modify or delete questions because the survey is already approved/active.',
                'code'    => 422 // كود منع معالجة منطقية للفرونت إند
            ];
        }
        // 2. التحقق من أن السؤال موجود ومربوط فعلياً بهذا الاستبيان 👈 (الحل هنا)
        $questionExistsInSurvey = DB::table('survey_questions')
            ->where('survey_id', $surveyId)
            ->where('question_id', $questionId) // أو بناءً على معرّف الجدول الأساسي لديك
            ->exists();

        if (!$questionExistsInSurvey) {
            return [
                'data'    => '',
                'message' => 'This question does not exist in the selected survey.',
                'code'    => 404 // 👈 كود 404 لأن العنصر غير موجود
            ];
        }

        // 3. التحقق من ربط المؤشر بالحملة (فقط إذا قام الفرونت إند بإرسال مؤشر جديد)
        if ($request->has('indicator_id') && $request->indicator_id !== null) {
            $isIndicatorLinked = $this->indicatorRepository->isLinkedToCampaign(
                $survey->campaign_id,
                (int) $request->indicator_id
            );

            if (!$isIndicatorLinked) {
                return [
                    'data'    => '',
                    'message' => 'The selected indicator is not linked to this campaign strategic goals.',
                    'code'    => 422
                ];
            }
        }

        // 4. بدء الـ Transaction بأمان بعد نجاح كل التحققات السابقة
        try {
            $result = DB::transaction(function () use ($surveyId, $questionId, $request) {

                // (باقي كود التحديث الذي قمنا بكتابته سابقاً دون أي تغيير)
                $questionData = [];
                if ($request->has('question_text')) $questionData['question_text'] = $request->question_text;
                if ($request->has('type')) {
                    $questionData['type'] = $request->type;
                    $questionData['scale'] = $request->type === 'rating' ? $request->scale : null;
                }

                if (!empty($questionData)) {
                    $this->SurveyQuestionRepository->update($questionId, $questionData);
                }

                if ($request->has('order')) {
                    $this->SurveyQuestionRepository->updateSurveyPivot($surveyId, $questionId, $request->order);
                }

                $pivotData = [];
                if ($request->has('indicator_id')) $pivotData['indicator_id'] = $request->indicator_id;
                if ($request->has('phase'))        $pivotData['phase'] = $request->phase;

                if (!empty($pivotData)) {
                    $this->SurveyQuestionRepository->updateIndicatorPivot($questionId, $pivotData);
                }

                return $this->CampaignSurveyRepository->getById($surveyId);
            });

            return [
                'data'    => new CampaignSurveyResource($result),
                'message' => 'Question updated successfully.',
                'code'    => 200
            ];

        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to update question: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }
    /**
     * حذف سؤال وفك كافة ارتباطاته من الاستبيان والمؤشرات
     */
    public function deleteQuestionFromSurvey(int $surveyId, int $questionId): array
    {
        // 1. التحقق من وجود الاستبيان
        $survey = $this->CampaignSurveyRepository->getById($surveyId);
        if (!$survey) {
            return ['data' => '', 'message' => 'Survey not found', 'code' => 404];
        }

        // 2. التحقق من وجود السؤال مرتبطاً بهذا الاستبيان فعلياً قبل الحذف
        $questionExists = DB::table('survey_questions')
            ->where('survey_id', $surveyId)
            ->where('question_id', $questionId)
            ->exists();
// يُوضع بعد فحص الـ if (!$survey) مباشرة
        if ($survey->status === 'active' || $survey->status === 'closed') {
            return [
                'data'    => '',
                'message' => 'Cannot modify or delete questions because the survey is already approved/active.',
                'code'    => 422 // كود منع معالجة منطقية للفرونت إند
            ];
        }
        if (!$questionExists) {
            return [
                'data'    => '',
                'message' => 'This question does not exist in the selected survey.',
                'code'    => 404
            ];
        }

        // 3. تنفيذ الحذف المتتالي الآمن
        try {
            $result = DB::transaction(function () use ($surveyId, $questionId) {

                // أ) مسح سجل الربط مع الاستبيان
                $this->SurveyQuestionRepository->deleteSurveyPivot($surveyId, $questionId);

                // ب) مسح سجل الربط مع المؤشرات والمراحل
                $this->SurveyQuestionRepository->deleteIndicatorPivot($questionId);

                // ج) مسح السؤال نفسه من جدول الأسئلة الرئيسي لعدم ترك بيانات يتيمة
                $this->SurveyQuestionRepository->deleteQuestion($questionId);

                // د) إعادة كائن الاستبيان محدثاً بعد نقص عدد الأسئلة منه
                return $this->CampaignSurveyRepository->getById($surveyId);
            });

            return [
                'data'    => '',
                'message' => 'Question deleted and unlinked successfully from the survey.',
                'code'    => 200
            ];

        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to delete question: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }
    /**
     * اعتماد الاستبيان وتحويل حالته إلى active
     */
    public function approveSurvey(int $surveyId): array
    {
        // 1. التحقق من وجود الاستبيان
        $survey = $this->CampaignSurveyRepository->getById($surveyId);
        if (!$survey) {
            return ['data' => '', 'message' => 'Survey not found', 'code' => 404];
        }

        // 2. التحقق مما إذا كان معتمداً بالفعل سابقاً
        if ($survey->status === 'active') {
            return [
                'data'    => '',
                'message' => 'This survey is already approved and active.',
                'code'    => 422
            ];
        }

        // 3. تحديث الحالة
        try {
            $this->CampaignSurveyRepository->approveSurvey($surveyId);

            // جلب البيانات المحدثة
            $updatedSurvey = $this->CampaignSurveyRepository->getById($surveyId);

            return [
                'data'    => new CampaignSurveyResource($updatedSurvey),
                'message' => 'Survey has been approved and published successfully.',
                'code'    => 200
            ];

        } catch (\Exception $e) {
            return [
                'data'    => '',
                'message' => 'Failed to approve survey: ' . $e->getMessage(),
                'code'    => 500
            ];
        }
    }
    /**
     * معالجة طلب جلب استبيان محدد بواسطة الـ ID
     */
    public function showById(int $surveyId): array
    {
        $survey = $this->CampaignSurveyRepository->getByIdWithDetails($surveyId);

        if (!$survey) {
            return [
                'data'    => '',
                'message' => 'Survey not found.',
                'code'    => 404
            ];
        }

        return [
            'data'    => new CampaignSurveyResource($survey), // تحويل البيانات لقالب الـ Resource المعتمد لديك
            'message' => 'Survey retrieved successfully.',
            'code'    => 200
        ];
    }
    /**
     * عرض استبيان حملة محددة بناءً على مرحلتها الزمنية
     */
    public function show(int $campaignId, string $stage): array
    {
        $campaign= $this->campaingRepository->getById($campaignId);
if(!$campaign){
    return [
        'data'    => '',
        'message' => 'No campaign found .',
        'code'    => 404
    ];

}
        // جلب الاستبيان من المستودع بأسلوب نظيف
        $survey = $this->CampaignSurveyRepository->get($campaignId, $stage);

        // إذا لم يتم العثور على استبيان لهذه الحملة في هذه المرحلة
        if (!$survey) {
            return [
                'data'    => '',
                'message' => 'No survey found for this campaign at the specified stage.',
                'code'    => 404
            ];
        }

        return [
            // تمرير الكائن المدمج بالأسئلة للـ Resource الخاص بك ليظهر بصيغة JSON منسقة
            'data'    => new CampaignSurveyResource($survey),
            'message' => 'Survey retrieved successfully.',
            'code'    => 200
        ];
    }}
