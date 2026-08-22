<?php

use App\Http\Controllers\CampaignAIRecommendationController;
use App\Http\Controllers\CampaignEvaluationController;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\CampaignReportExportController;
use App\Http\Controllers\CampaignSurveyController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\courseController;
use App\Http\Controllers\GeneralDashboardController;
use App\Http\Controllers\GeneralDashboardDetailsController;
use App\Http\Controllers\TeamRequestController;
use App\Http\Controllers\evaluationTaskController;
use App\Http\Controllers\MonitoringGoalController;
use App\Http\Controllers\PointTransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\VolunteerRequestController;
use App\Services\ApprovalRequestService;
use App\Services\KPIBrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('verify', [AuthController::class, 'verify']);
Route::post('resend-verification-code',[AuthController::class, 'resendVerificationCode']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('role.throttle');
Route::middleware(['auth:sanctum','check.banned'])->group(function () {
    //قسم الحملة
    Route::post('create_Campanig', [CampaignController::class, 'create']);
     //   ->middleware('can:create.campaign');

        Route::get('showCampanigVoulnterrs/{id}', [CampaignController::class, 'showCampanigVoulnterrs']);

    Route::get('show_Campanig', [CampaignController::class, 'show']);
    Route::post('indexDetail_Campanig/{id}', [CampaignController::class, 'indexDetail']);
    Route::post('SearchCampaign', [CampaignController::class, 'SearchCampaign']);
    Route::post('assignCampaignLeader/{campaignId}/{userId}', [CampaignController::class,
        'assignCampaignLeader']);

        Route::post('updateCampanig/{campaignId}/', [CampaignController::class, 'update']);

        Route::get('showCamanigEvaulation', [CampaignController::class,
            'showCamanigEvaulation']);
    Route::get('showAllEmployee', [UserController::class, 'showAllEmployee']);
    Route::post('UpdateEmployee/{id}', [UserController::class, 'UpdateEmployee']);
    Route::post('ShowdetailEmployee/{id}', [UserController::class, 'ShowdetailEmployee']);
    Route::put('/updateStatusUser/{id}', [UserController::class, 'updateStatusUser']);
//
    Route::post('logout', [UserController::class, 'logout']) ;
    //قسم بروفايل
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profileupdate', [UserController::class, 'updateProfile']);
    Route::post('volunteerjoin', [VolunteerRequestController::class, 'store']);
    //ادارة الاقسام
    Route::post('storeDepartment', [DepartmentController::class, 'store']);
    Route::get('showAllDepartment', [DepartmentController::class, 'index']);
    Route::post('addEmployee', [UserController::class, 'addUser']);
        Route::post('assignDepartmentManager/{id}/{user_id}', [UserController::class, 'assignDepartmentManager']);
    //ادارة الادوار
    Route::get('getRoleNames', [RoleController::class, 'getRoleNames']);
    Route::post('AddRoleForDepartment', [RoleController::class, 'AddRoleForDepartment']);
        Route::post('showRoleForDepartment/{id}', [RoleController::class, 'showRoleForDepartment']);

        Route::post('AddRole', [RoleController::class, 'AddRole']);

    Route::post('updateRole/{id}', [RoleController::class, 'updateRole']);
    Route::delete('DeleteRole/{id}', [RoleController::class, 'DeleteRole']);
    Route::get('getAllRoles', [RoleController::class, 'getAllRoles']);
    Route::post('SearchForRoles', [\App\Http\Controllers\RoleController::class, 'SearchForRoles']);
    Route::get('getAllPermissions', [\App\Http\Controllers\PermissionController::class, 'getAllPermissions']);
    Route::post('getAllPermissionsForRoleInDepartment/{DEPARTMENT_ID}/{ROLE_ID}',
        [\App\Http\Controllers\PermissionController::class, 'getAllPermissionsForRoleInDepartment']);
    Route::post('AddPermission', [\App\Http\Controllers\PermissionController::class, 'AddPermission']);
    Route::post('AddPermissionToRole', [\App\Http\Controllers\PermissionController::class, 'AddPermissionToRole']);
    Route::post('updatePermission/{id}', [\App\Http\Controllers\PermissionController::class, 'updatePermission']);
    Route::delete('DeletePermission/{id}', [\App\Http\Controllers\PermissionController::class, 'DeletePermission']);
    Route::delete('deletePermissionForRole/{department_id}/{role_id}/{permission_id}', [\App\Http\Controllers\PermissionController::class, 'deletePermissionForRole']);
    Route::post('SearchForPermissions', [\App\Http\Controllers\PermissionController::class, 'SearchForPermissions']);

//قسم الكورسات
    Route::post('addCourse', [\App\Http\Controllers\courseController::class, 'create']);
      //  ->middleware('can:add.course');
    Route::get('indexAllCourses', [\App\Http\Controllers\courseController::class, 'index']);
    Route::post('indexDetailCourse/{id}', [\App\Http\Controllers\courseController::class, 'show']);
    Route::post('courses/enroll/{id}', [CourseController::class, 'store']);
    Route::get('showMyCourses', [CourseController::class, 'showMyCourses']);

//قسم  ادارة المستخدمين
    Route::get('showAllUsers', [UserController::class, 'showAllEmployee']);
    Route::post('UpdateUser/{id}', [UserController::class, 'UpdateEmployee']);
    Route::post('ShowdetailUser/{id}', [UserController::class, 'ShowdetailEmployee']);
    Route::post('searchUser', [UserController::class, 'searchUser']);
    Route::post('searchUserByName', [UserController::class, 'searchUser']);
    Route::post('searchUserByRole', [UserController::class, 'searchUser']);
        Route::post('searchVolunteer', [UserController::class, 'searchVolunteer']);


    Route::get('ShowAllRoles', [UserController::class, 'ShowAllRoles']);
        Route::put('updateStatusUser/{id}', [UserController::class, 'updateStatusUser']);
    Route::post('searchVolunteer', [UserController::class, 'searchVolunteer']);
    Route::get('ShowAllRoles', [UserController::class, 'ShowAllRoles']);

     Route::put('updateStatusUser/{id}', [UserController::class, 'updateStatusUser']);

    Route::get('showPointForUser', [PointTransactionController::class, 'showPointForUser']);
        Route::post('addPonus', [PointTransactionController::class, 'addPonus']);
    Route::post('showPointForVolunteer/{id}', [PointTransactionController::class, 'showPointForVolunteer']);
//فسم الحضور
    Route::post('leaderCheckIn/{id}', [AttendanceController::class, 'leaderCheckIn']);
      //ju]تعديل   ->middleware('can:record.attendance');
    Route::post('leaderCheckOut/{id}', [AttendanceController::class, 'leaderCheckOut']);
    Route::post('campaignAttendances/{id}', [AttendanceController::class, 'campaignAttendances']);
    Route::get('volunteerAttendances', [AttendanceController::class, 'volunteerAttendances']);
    Route::post('attendance/scan-qr', [AttendanceController::class, 'scanVolunteerQr']);
    //قسم المتطوعين
    Route::get('top-volunteers', [UserController::class, 'getTopVolunteers']);
    Route::get('getVoulnteer', [UserController::class, 'getVoulnteer']);
    Route::post('showVolunteer/{id}', [UserController::class, 'showVolunteer']);
//طلبات موافقة
    Route::get('showAllApprovalRequest', [ApprovalRequestController::class, 'showAll']);
    Route::post('updateStatusApprovalRequest/{id}', [ApprovalRequestController::class, 'updateStatus']);
    Route::post('indexDetailApprovalRequest/{id}', [ApprovalRequestController::class, 'indexDetail']);
///////////////////////////////////////////////////////////
/// kpi
    Route::post('indexAllGoals/{id}', [\App\Http\Controllers\MonitoringGoalController::class, 'index']);
    Route::post('showIndicatorsForGoals/{id}', [\App\Http\Controllers\MonitoringGoalController::class, 'show']);
    Route::post('updateStatusIndicator/{goal_id}/{indicator_id}', [\App\Http\Controllers\MonitoringGoalController::class, 'updateStatus']);
    Route::post('showSurveyByStage/{id}', [\App\Http\Controllers\CampaignSurveyController::class, 'show']);
    Route::post('addQuestionToSurvey/{id}', [\App\Http\Controllers\CampaignSurveyController::class, 'addQuestionToSurvey']);
    Route::post('updateQuestionToSurvey/{survey_id}/{question_id}', [\App\Http\Controllers\CampaignSurveyController::class, 'updateQuestionToSurvey']);
    Route::post('DeleteQuestionToSurvey/{survey_id}/{question_id}', [\App\Http\Controllers\CampaignSurveyController::class, 'DeleteQuestionToSurvey']);
    Route::delete('deleteQuestionToSurvey/{survey_id}/{question_id}', [\App\Http\Controllers\CampaignSurveyController::class, 'deleteQuestionToSurvey']);
    Route::post('approveSurvey/{survey_id}', [\App\Http\Controllers\CampaignSurveyController::class, 'approveSurvey']);
    Route::post('showBySurveyId/{survey_id}', [\App\Http\Controllers\CampaignSurveyController::class, 'showBySurveyId']);
    Route::get('/campaigns/{campaign}', [CampaignEvaluationController::class, 'dashboard']);
    Route::get('/showResultIndicators/{campaign_id}/{indicator_id}', [MonitoringGoalController::class, 'showResultIndicators']);
    Route::post('setWeight', [MonitoringGoalController::class, 'setWeight']);
    Route::post('setTargetValue', [MonitoringGoalController::class, 'setTargetValue']);

    Route::get(
        '/surveys/{survey}/results',
        [CampaignSurveyController::class, 'results']
    );////////////////////////////taskEvaluation
    Route::post('storeEvaluationTask',
        [\App\Http\Controllers\evaluationTaskController::class, 'store']);
    Route::get('indexAllEvaluationTask', [\App\Http\Controllers\evaluationTaskController::class, 'index']);

/////////////////////////////////////
///
//توصيات
    Route::get('/campaigns/{campaign}/ai-recommendations/pre-launch', [CampaignAIRecommendationController::class, 'preLaunchRecommendations'])->name('campaigns.ai-recommendations.pre-launch');
    Route::get('/campaigns/{campaign}/ai-recommendations/post-launch', [CampaignAIRecommendationController::class, 'postLaunchRecommendations'])->name('campaigns.ai-recommendations.post-launch');
//احصائيات
    Route::get('/general-dashboard/kpis', [GeneralDashboardController::class, 'kpis']);
    Route::get('/general-dashboard/statistics', [GeneralDashboardController::class, 'statistics']);
    Route::get('/general-dashboard/details', [GeneralDashboardDetailsController::class, 'getDetails']);
    Route::get('/general-dashboard/overview', [GeneralDashboardController::class, 'overview']
    );
//تقرير
    Route::get('/campaign-reports/{campaignId}', [CampaignReportController::class, 'show']
    );
    Route::get(
        '/campaigns/{campaignId}/reports/pdf',
        [CampaignReportExportController::class, 'pdf']
    );
    Route::get(
        '/campaigns/{campaignId}/report/excel',
        [CampaignReportExportController::class, 'excel']
    )->whereNumber('campaignId');


    /////////////////////////////////////////////////////////////
    // --- راوتات طلبات التطوع ---
    // 1. تقديم طلب جديد (للمستخدم)
    Route::post('volunteerjoin', [VolunteerRequestController::class, 'store']);
    // 2. عرض جميع الطلبات المعلقة (للأدمن أو من لديه صلاحية)
    Route::get('showAllVolunteerRequests', [VolunteerRequestController::class, 'index']);
    // 3. عرض تفاصيل طلب واحد
    Route::get('showVolunteerRequest/{id}', [VolunteerRequestController::class, 'show']);
    // 4. قبول أو رفض الطلب
    Route::post('updateVolunteerRequestStatus/{id}', [VolunteerRequestController::class, 'updateStatus']);
    // رابط عرض البطاقة للمتطوع
    Route::get('/my-card', [VolunteerRequestController::class, 'getMyIDCard']);
    Route::get('top-volunteers', [UserController::class, 'getTopVolunteers']);
    Route::post('campaignsjoin/{campaignId}', [CampaignController::class, 'joinCampaign']);

//////تطوع للحملة ك فريق   Route::post('/team-request', [TeamRequestController::class, 'create']);
  Route::get('available-volunteers/{campaign_id}', [TeamRequestController::class, 'getAvailableVolunteers']);
  Route::post('team-request', [TeamRequestController::class, 'create']);
    Route::post('team-request-accept/{id}', [TeamRequestController::class, 'accept']);

    Route::post('team-request-reject/{id}', [TeamRequestController::class, 'reject']);
    Route::get('showMyCampanig', [CampaignController::class, 'showMyCampanig']);

    /////////////////////Complaint with Raya heeeeeeeeheeeee
// metadata (ثابت)
Route::get('meta-data', [ComplaintController::class, 'metaData']);

// filter (ثابت)
Route::get('complaints/filter', [ComplaintController::class, 'filter']);

// list
Route::get('showComplaints', [ComplaintController::class, 'index']);

// create
Route::post('addcomplaints', [ComplaintController::class, 'store']);

// review
Route::post('complaintsreview/{id}', [ComplaintController::class, 'review']);

// show single (لازم يكون آخر شيء)
Route::get('complaints/{id}', [ComplaintController::class, 'show'])
    ->whereNumber('id');
///قسم الاشعارات
Route::post('/update-device-token', [DeviceTokenController::class, 'updateDeviceToken']);

Route::get('/my-notifications', [VolunteerRequestController::class, 'getMyNotifications']);
////////////موظف المرااقبة والتقييم
Route::get('evaluation-my-tasks',[evaluationTaskController::class, 'myTasks']);
Route::get('evaluation-tasks-questions/{id}', [evaluationTaskController::class, 'getQuestions']);
Route::post('evaluation-tasks-submit/{id}', [evaluationTaskController::class, 'submitAnswers']);
Route::post('evaluation-tasks-status/{id}', [evaluationTaskController::class, 'updateStatus']);
// مسارات Stripe  الدفع

Route::post('/donations/checkout', [DonationController::class, 'createCheckoutSession']);
});
Route::get('/general-dashboard/overview', [GeneralDashboardController::class, 'overview']
);

Route::get('/donations/success', [DonationController::class, 'paymentSuccess']);
Route::get('/donations/cancel', [DonationController::class, 'paymentCancel']);
Route::get(
    '/campaigns/{campaignId}/donations',
    [DonationController::class, 'campaignDonations']
);