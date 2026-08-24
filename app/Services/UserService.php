<?php

namespace App\Services;

use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\searchUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ResendVerificationRequest;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VolunteerDetailsResource;
use App\Http\Resources\VolunteerListResource;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Repositories\DepartmentRepository;
use App\Repositories\DepartmentRolePermissionRepository;
use App\Repositories\DepartmentRoleRepository;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\userRepository;
use App\Services\Authorization\UserAuthorizationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserStatusMail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    use AuthorizesRequests;

    protected $userRepository;
    protected $emailRepository;
    private DepartmentRepository $departmentRepository;
    private RoleRepository $roleRepository;

    private DepartmentRoleRepository $departmentRoleRepository;
    private UserAuthorizationService $userAuthorizationService;

    public function __construct(
        RoleRepository $roleRepository,
        DepartmentRepository $departmentRepository,
        userRepository $userRepository,
        EmailVerficationRepository $emailRepository,
        DepartmentRoleRepository $departmentRoleRepository,
            UserAuthorizationService $userAuthorizationService

    ) {
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
        $this->roleRepository = $roleRepository;
        $this->departmentRoleRepository = $departmentRoleRepository;
        $this->userAuthorizationService = $userAuthorizationService;

    }

    protected function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function register(UserRequest $request): array
    {
        return DB::transaction(function () use ($request) {
            $user = $this->userRepository->create($request->validated());
            $code = $this->generateVerificationCode();

            $this->emailRepository->deleteByEmail($request->email);
            $verification = $this->emailRepository->create($request->email, $code);

            Mail::to($user->email)->send(new EmailVerificationMail($code));

            return [
                'user' => $user,
                'verification' => $verification,
                'message' => 'Registration success. Please check your email to verify your account',
                'code' => 201
            ];
        });
    }

    public function Verify(EmailVerificationRequest $request): array
    {
        $emailverfication = $this->emailRepository->exists($request);

        if (!$emailverfication) {
            return [
                'user' => null,
                'message' => 'Invalid or expired Verification code',
                'code' => 400
            ];
        }

        $user = $this->userRepository->getByEmail($request->email);
        $user->email_verified_at = Carbon::now();
        $user->save();
        $emailverfication->delete();

        return [
            'user' => $user,
            'message' => 'تم تأكيد حسابك بنجاح يمكنك الان تسجيل الدخول',
            'code' => 200,
        ];
    }
    public function login(LoginRequest $request): array
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return [
                'user' => null,
                'message' => 'Invalid credentials',
                'code' => 401
            ];
        }

        $authUser = Auth::user();

        if ($authUser->status === 'banned') {
            Auth::logout();
            return [
                'user' => null,
                'message' => 'Your account is banned. Please contact admin.',
                'code' => 403
            ];
        }

        if (is_null($authUser->email_verified_at)) {
            return [
                'user' => null,
                'message' => 'Email not verified',
                'code' => 403
            ];
        }

        $user = User::with([
            'departmentRole.role',
            'departmentRole.department',
            'departmentRole.permissions'
        ])->find($authUser->id);

        $permissions = $user->departmentRole?->permissions->pluck('name')->values()->toArray() ?? [];
        $role = $user->departmentRole?->role?->name ?? 'No Role';
        $department = $user->departmentRole?->department?->name ?? 'No Department';

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department' => $department,
                'role' => $role,
                'permissions' => $permissions,
                'token' => $token,
            ],
            'token' => $token,
            'message' => 'Login successful',
            'code' => 200
        ];
    }
        public function logout(): array
    {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()->delete();
            $message = 'User logged out successfully';
            $code = 200;
        } else {
            $message = 'Invalid token';
            $code = 404;
        }

        return [
            'user' => null,
            'message' => $message,
            'code' => $code
        ];
    }





    public function assignDepartmentManager($departmentId, $userId)
    {
        $department = $this->departmentRepository->find($departmentId);

        if (!$department) {
            return [
                'user' => null,
                'message' => 'Department not found',
                'code' => 404
            ];
        }

        $user = $this->userRepository->getById($userId);

        if (!$user) {
            return [
                'user' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        $managerRole = $this->roleRepository->findByName('Manager');

        if (!$managerRole) {
            return [
                'user' => null,
                'message' => 'Manager role not found',
                'code' => 404
            ];
        }

        $departmentRole = $this->departmentRoleRepository
            ->findByDepartmentAndRole($department->id, $managerRole->id);

        if (!$departmentRole) {
            return [
                'user' => null,
                'message' => 'Manager role does not belong to this department',
                'code' => 404
            ];
        }

        return DB::transaction(function () use ($department, $user, $departmentRole) {

            $department->manager_id = $user->id;
            $department->save();

            $updatedUser=$this->userRepository->updateStatusUser([
                'department_role_id' => $departmentRole->id,
            ], $user);

            // نظاما أدوار متوازيان: department_role_id هو ما تكتبه الواجهة،
            // لكن hasRole()/User::role() يقرآن جداول Spatie. بدون هذه المزامنة
            // تبقى model_has_roles فارغة فلا يُعثر على أي مستلم للإشعارات.
            $this->syncSpatieRole($updatedUser, $departmentRole);

            return [
                'user' => $updatedUser,
                'message' => 'Manager assigned successfully',
                'code' => 200
            ];
        });
    }
    /**
     * يزامن دور Spatie للمستخدم انطلاقاً من department_role_id.
     *
     * النظام يخزّن الدور في عمود department_role_id، بينما تعتمد استعلامات
     * الإشعارات على User::role()/hasRole() اللذين يقرآن model_has_roles.
     * بدون هذه المزامنة ينفصل النظامان فتفشل الإشعارات بصمت.
     */
    private function syncSpatieRole($user, $departmentRole): void
    {
        $roleName = $departmentRole->role?->name;

        if (!$user || !$roleName) {
            return;
        }

        $user->syncRoles([$roleName]);
    }

    public function getVisibleUsers($authUser): array
    {
        $data = $this->userRepository->getAll();
        $array = [];

        foreach ($data as $user) {
            if ($this->userAuthorizationService->canView($authUser, $user)) {
                $array[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status ?? 'active',
                    'role' => $user->departmentRole?->role
                        ? [$user->departmentRole->role->name]
                        : [],
                  'department_name' => $user->departmentRole?->department?->name];
            }
        }

        return [
            'user' => $array,
            'message' => 'employees successfully',
            'code'=>200
        ];
    }    public function searchUser(searchUserRequest $request): array
    {
        $user = $this->userRepository->searchUser($request);

        return [
            'users' => UserResource::collection($user),
            'meta' => [
                'current_page' => $user->currentPage(),
                'last_page' => $user->lastPage(),
                'per_page' => $user->perPage(),
                'total' => $user->total(),
            ],
            'message' => 'Users retrieved successfully',
            'code' => 200
        ];
    }

    public function UpdateEmployee($request, $id): array
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return [
                'user' => null,
                'message' => 'this user not found',
                'code' => 404
            ];
        }

        if (! $this->userAuthorizationService->canUpdate(Auth::user(), $user)) {
            return [
                'user' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        $user = $this->userRepository->UpdateEmployee(
            $request->validated(),
            $id
        );

        return [
            'user' => new UserDetailResource($user),
            'message' => 'success',
            'code' => 200
        ];
    }
    public function ShowdetailEmployee($id): array
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return [
                'user' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        if (! $this->userAuthorizationService->canView(Auth::user(), $user)) {
            return [
                'user' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ];
        }

        return [
            'user' => new UserDetailResource($user),
            'message' => 'User retrieved successfully',
            'code' => 200
        ];
    }
    public function ShowAllRoles(): array
    {
        $roles = $this->userRepository->ShowAllRoles();

        return [
            'user' => $roles,
            'message' => 'Roles retrieved successfully',
            'code' => 200
        ];
    }

    public function getVoulnteer()
    {
        $Volunteer = $this->userRepository->getVoulnteer();

        return [
            'user' => VolunteerListResource::collection($Volunteer),
            'message' => 'Volunteer retrieved successfully',
            'code' => 200
        ];
    }

    public function showVolunteer($id)
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return ['user' => null, 'message' => 'Volunteer not found', 'code' => 404];
        }

        return [
            'user' => new VolunteerDetailsResource($user),
            'message' => 'Volunteer retrieved successfully',
            'code' => 200
        ];
    }

    public function profile()
    {
        $user = Auth::user();

        return [
            'user' => new UserResource($user),
            'message' => 'Profile retrieved successfully',
            'code' => 200
        ];
    }

    public function updateStatusUser(UpdateUserStatusRequest $request, $id)
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return ['user' => null, 'message' => 'User not found', 'code' => 404];
        }

      //  $this->authorize('update', $user);

        $data = [
            'status' => $request->status,
            'ban_reason' => $request->ban_reason,
        ];

        $user = $this->userRepository->updateStatusUser($data, $user);

        if ($user->status === 'banned') {

            Mail::to($user->email)->send(
          new UserStatusMail(
            'Account Suspended',
            'Your account has been suspended. Reason: ' . ($user->ban_reason ?? 'Not specified')
        )
    );

} else {

    Mail::to($user->email)->send(
        new UserStatusMail(
            'Account Reactivated',
            'Your account has been reactivated and you can now use the system again.'
        )
    );
}

        return [
            'user' => new UserResource($user),
            'message' => $user->status === 'banned' ? 'User banned successfully' : 'User activated successfully',
            'code' => 200
        ];
    }

    public function resendVerificationCode(ResendVerificationRequest $request): array
    {
        $user = $this->userRepository->getByEmail($request->email);

        if (!$user) {
            return [
                'user' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        if ($user->email_verified_at) {
            return [
                'user' => null,
                'message' => 'Email already verified',
                'code' => 400
            ];
        }

        $verification = $this->emailRepository->getLatestByEmail($request->email);

        if ($verification && $verification->created_at->gt(now()->subMinute())) {
            return [
                'user' => null,
                'message' => 'Please wait before requesting another code',
                'code' => 429
            ];
        }

        $code = $this->generateVerificationCode();

        $this->emailRepository->deleteByEmail($request->email);
        $this->emailRepository->create($request->email, $code);

        Mail::to($request->email)->send(new EmailVerificationMail($code));

        return [
            'user' => null,
            'message' => 'Verification code sent successfully',
            'code' => 200
        ];
    }
    public function createUser(array $data)
    {
        if (! $this->userAuthorizationService->canCreate(auth()->user(), $data)) {
            return [
                'user' => null,
                'message' => 'You are not allowed to create a user in this department or with this role.',
                'code' => 403,
            ];
        }
        $departmentRole = $this->departmentRoleRepository
            ->findByDepartmentAndRole(
                $data['department_id'],
                $data['role']
            );
        if (!$departmentRole) {
            return [
                'user' => null,
                'message' => 'This role does not belong to the selected department.',
                'code' => 404
            ];
        }

        return DB::transaction(function () use ($data, $departmentRole) {

            $user = $this->userRepository->create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'password'           => Hash::make($data['password']),
                'phone'              => $data['phone'] ?? null,
                'department_role_id' => $departmentRole->id,
                'email_verified_at'  => now(),
            ]);

            // انظر الملاحظة في assignDepartmentManager: لا بد من مزامنة دور
            // Spatie وإلا لم تصل الإشعارات لهذا المستخدم.
            $this->syncSpatieRole($user, $departmentRole);

            if ($departmentRole->role?->name === 'Manager') {

                $department = $this->departmentRepository->find($data['department_id']);

                if ($department) {
                    $department->update([
                        'manager_id' => $user->id,
                    ]);
                }
            }

            return [
                'user' => new UserResource($user),
                'message' => 'User created successfully.',
                'code' => 201,
            ];
        });    }       public function searchVolunteer(searchUserRequest $request)
    {
        $user = $this->userRepository->searchVolunteer($request);

        return [
            'users' => UserResource::collection($user),
            'meta' => [
                'current_page' => $user->currentPage(),
                'last_page' => $user->lastPage(),
                'per_page' => $user->perPage(),
                'total' => $user->total(),
            ],
            'message' => 'Users retrieved successfully',
            'code' => 200
        ];

    }
}
