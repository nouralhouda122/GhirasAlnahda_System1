<?php

namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\AddPermission;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\searchUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function profile()
    {
        $data = $this->userService->profile();

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        }
        return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }


    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->hasFile('image')) {
            if ($user->getRawOriginal('image')) {
                Storage::disk('public')->delete($user->getRawOriginal('image'));
            }
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }
        $user->save();
        if ($user->image) {
            $user->image = asset('storage/' . $user->image);
        }
        return ResponseHelper::Success($user, 'تم تحديث البروفايل بنجاح', 200);
    }

    public function card()
    {
        $user = auth()->user();
        return ResponseHelper::Success([
            'fullName' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'qr_code' => asset('storage/' . $user->volunteerProfile->qr_code),
        ], 'User profile data', 200);
    }
    public function assignDepartmentManager( $department_is,$user_id)
    {
        $data = $this->userService->assignDepartmentManager($department_is,$user_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        }

        return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }

    public function addUser(addUserRequest $request)
    {
        $this->authorize('create', [User::class, $request->role]);
        $data = $this->userService->createUser($request->validated());

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        }

        return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }
    public function showAllEmployeeCampanig(  ){
        $data=$this->userService->getVisibleUsers(Auth::user());
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}
    public function searchUser(searchUserRequest $request){
        $data=$this->userService->searchUser($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success([
                'data' => $data['users'],
                'meta' => $data['meta']
            ], $data['message'], $data['code']);        }
        else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}

    public function UpdateEmployee(UpdateUserRequest $request,$id){
        $data=$this->userService->UpdateEmployee($request,$id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}
    public function ShowdetailEmployee($id){
        $data=$this->userService->ShowdetailEmployee($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}

//عرض كل الادوار
    public function ShowAllRoles(){
        $data=$this->userService->ShowAllRoles();
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}

//عرض متطوعين
    public function getVoulnteer( )
    {
        $data=$this->userService->getVoulnteer();
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}
// عرض تفاصيل متطوع
    public function showVolunteer($id)    {
        $data=$this->userService->showVolunteer($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }}
//عرض سجل نقاط متطوعين
//تعديل حالة يوزر(حظر /الغاء حظر)
    public function updateStatusUser($id, UpdateUserStatusRequest $request)
    {
        $data = $this->userService->updateStatusUser($request, $id);
        if ($data['code'] === 200) {
           return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
      } else {
           return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }


}
