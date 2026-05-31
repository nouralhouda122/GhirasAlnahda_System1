<?php

use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\courseController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\IndicatorGeneratorController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\PointTransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VolunteerRequestController;
use App\Services\ApprovalRequestService;
use App\Services\AIUnderstandingService;
use App\Services\KPIBrain;
use App\Services\KPIBrainService;
use App\Services\KPIEngineService;
use App\Services\KPIExtractorService;
use App\Services\KpiUnderstandingService;
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
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('role.throttle');
Route::middleware(['auth:sanctum','check.banned'])->group(function () {
    //قسم الحملة
    Route::post('create_Campanig', [CampaignController::class, 'create'])
        ->middleware('can:create.campaign');
    Route::get('show_Campanig', [CampaignController::class, 'show']);
    Route::post('indexDetail_Campanig/{id}', [CampaignController::class, 'indexDetail']);
    Route::post('SearchCampaign', [CampaignController::class, 'SearchCampaign']);
    Route::post('assignCampaignLeader/{campaignId}/{userId}', [CampaignController::class,
        'assignCampaignLeader']);
        Route::get('showCamanigEvaulation', [CampaignController::class,
            'showCamanigEvaulation']);
    Route::get('showAllEmployee', [UserController::class, 'showAllEmployeeCampanig']);
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
    Route::post('addUser', [UserController::class, 'addUser']);
        Route::post('assignDepartmentManager/{id}/{user_id}', [UserController::class, 'assignDepartmentManager']);
    //ادارة الادوار
    Route::get('getRoleNames', [RoleController::class, 'getRoleNames']);
    Route::post('AddRoleForDepartment/{id}', [RoleController::class, 'AddRoleForDepartment']);
        Route::post('showRoleForDepartment/{id}', [RoleController::class, 'showRoleForDepartment']);

    Route::post('updateRole/{id}', [RoleController::class, 'updateRole']);
    Route::delete('DeleteRole/{id}', [RoleController::class, 'DeleteRole']);
    Route::get('getAllRoles', [RoleController::class, 'getAllRoles']);
    Route::post('SearchForRoles', [\App\Http\Controllers\RoleController::class, 'SearchForRoles']);
    Route::get('getAllPermissions', [\App\Http\Controllers\PermissionController::class, 'getAllPermissions']);
    Route::post('getAllPermissionsForRole/{id}', [\App\Http\Controllers\PermissionController::class, 'getAllPermissionsForRole']);
    Route::post('AddPermission', [\App\Http\Controllers\PermissionController::class, 'AddPermission']);
    Route::post('AddPermissionToRole/{id}/{permission_id}', [\App\Http\Controllers\PermissionController::class, 'AddPermissionToRole']);
    Route::post('updatePermission/{id}', [\App\Http\Controllers\PermissionController::class, 'updatePermission']);
    Route::post('updatePermissionForRole/{id}/{permission_id}', [\App\Http\Controllers\PermissionController::class, 'updatePermissionForRole']);
    Route::delete('DeletePermission/{id}', [\App\Http\Controllers\PermissionController::class, 'DeletePermission']);
    Route::delete('deletePermissionForRole/{id}/{permission_id}', [\App\Http\Controllers\PermissionController::class, 'DeletePermission']);
    Route::post('SearchForPermissions', [\App\Http\Controllers\PermissionController::class, 'SearchForPermissions']);

//قسم الكورسات
    Route::post('addCourse', [\App\Http\Controllers\courseController::class, 'create'])
        ->middleware('can:add.course');
    Route::get('indexAllCourses', [\App\Http\Controllers\courseController::class, 'index']);
    Route::post('indexDetailCourse/{id}', [\App\Http\Controllers\courseController::class, 'show']);
    Route::post('courses/enroll/{id}', [CourseController::class, 'store']);
    //كورسات خاص بالمتطوع راية
    Route::get('showMyCourses', [CourseController::class, 'showMyCourses']);

//قسم  ادارة المستخدمين
    Route::get('showAllUsers', [UserController::class, 'showAllEmployeeCampanig']);
    Route::post('UpdateUser/{id}', [UserController::class, 'UpdateEmployee']);
    Route::post('ShowdetailUser/{id}', [UserController::class, 'ShowdetailEmployee']);
    Route::post('searchUser', [UserController::class, 'searchUser']);
    Route::post('searchUserByName', [UserController::class, 'searchUser']);
    Route::post('searchUserByRole', [UserController::class, 'searchUser']);
    Route::get('ShowAllRoles', [UserController::class, 'ShowAllRoles']);

        Route::put('updateStatusUser/{id}', [UserController::class, 'updateStatusUser']);

    Route::get('showPointForUser', [PointTransactionController::class, 'showPointForUser']);
    Route::post('showPointForVolunteer/{id}', [PointTransactionController::class, 'showPointForVolunteer']);
//فسم الحضور
    Route::post('leaderCheckIn/{id}', [AttendanceController::class, 'leaderCheckIn']);
      //ju]تعديل   ->middleware('can:record.attendance');
    Route::post('leaderCheckOut/{id}', [AttendanceController::class, 'leaderCheckOut']);
    Route::post('campaignAttendances/{id}', [AttendanceController::class, 'campaignAttendances']);
    Route::get('volunteerAttendances', [AttendanceController::class, 'volunteerAttendances']);
    Route::post('attendance/scan-qr', [AttendanceController::class, 'scanVolunteerQr']);
    //قسم المتطوعين

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

    Route::get('showMyCampanig', [CampaignController::class, 'showMyCampanig']);

    /////////////////////Complaint with Raya heeeeeeeeheeeee

    //توضيح للحساسيات وانواعها
    Route::get('complaintsMeta-data', [ComplaintController::class, 'metaData']);
    Route::get('showComplaints', [ComplaintController::class, 'index']);
    Route::post('Addcomplaints', [ComplaintController::class, 'store']);

    // مسار معالجة ورد الإدارة على شكوى معينة
    Route::put('complaints/{id}/review', [ComplaintController::class, 'review']);






});
