<?php
namespace App\Services;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\searchUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VolunteerDetailsResource;
use App\Http\Resources\VolunteerListResource;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\userRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserService
{
    use AuthorizesRequests;

    protected $userRepository;
    protected $emailRepository;
    private DepartmentRepository $departmentRepository;
    private RoleRepository $roleRepository;

    public function __construct(  RoleRepository $roleRepository,DepartmentRepository $departmentRepository,userRepository $userRepository, EmailVerficationRepository $emailRepository)
    {
        $this->departmentRepository=$departmentRepository;
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
        $this->roleRepository = $roleRepository;

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
        $user = Auth::user();
        if ($user->status === 'banned') {
            Auth::logout();
            return [
                'user' => null,
                'message' => 'Your account is banned. Please contact admin.',
                'code' => 403
            ];
        }
        if (is_null($user->email_verified_at)) {
            return [
                'user' => null,
                'message' => 'Email not verified',
                'code' => 403
            ];
        }
        $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);
        $user = User::with('roles.permissions', 'permissions')->find($user->id);
        $user = $this->appendRolesAndPermission($user);
        $user['token'] = $user->createToken('token')->plainTextToken;
        return [
            'user' => $user,
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
        $department=$this->departmentRepository->find($departmentId);
        if (!$department) {
            return [
                'user'=>null,
                'message' => 'Department not found',
                'code' => 404
            ];
        }

$user=$this->userRepository->getById($userId);
        if (!$user) {
            return [
                'user'=>null,
                'message' => 'User not found',
                'code' => 404
            ];
        }
        $department->manager_id = $user->id;
        $department->save();
        return [
            'user'=>$user,
            'message' => 'Manager assigned successfully',
            'code' => 200
        ];
    }
    public function createUser(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $user = $this->userRepository->create_User($data);

            $role=$this->roleRepository->findRoleByDepartment($data['department_id']);
            if (!$role) {
                return [
                    'user' => null,
                    'message' => 'Invalid role for this department',
                    'code' => 422
                ];
            }

            $user->assignRole($role->name);

            if (str_contains(strtolower($role->name), 'manager')) {

                $department = $this->departmentRepository->find($data['department_id']);

                if (!$department) {
                    return [
                        'user' => null,
                        'message' => 'Department not found',
                        'code' => 404
                    ];
                }

                $department->manager_id = $user->id;
                $department->save();
            }
            return [
                'user' => $user,
                'message' => 'Success',
                'code' => 200
            ];
        });
    }       private function appendRolesAndPermission($user)
    {
        $roles = $user->roles->pluck('name')->toArray();
        unset($user['roles']);
        $user['roles'] = $roles;

        $permissions = $user->permissions->pluck('name')->toArray();
        unset($user['permissions']);
        $user['permissions'] = $permissions;

        return $user;
    }

    public function getVisibleUsers($Auth_user): array
    {
        $data = $this->userRepository->getAll();
        $array = [];

        foreach ($data as $user) {
            if ($Auth_user->id !== $user->id && $Auth_user->can('view', $user)) {
                $array[] = $user;
            }
        }

        return [
            'user' => UserResource::collection($array),
            'message' => 'successfully',
            'code' => 200
        ];
    }

    public function searchUser(searchUserRequest $request): array
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
            return ['user' => null, 'message' => 'this user not found', 'code' => 404];
        }
        $this->authorize('update', $user);
        $user = $this->userRepository->UpdateEmployee($request->validated(), $id);
        return [
            'user' => new UserResource($user),
            'message' => 'success',
            'code' => 200
        ];
    }

    public function ShowdetailEmployee($id): array
    {
        $user = $this->userRepository->getById($id);
        if (!$user) {
            return ['user' => null, 'message' => 'User not found', 'code' => 404];
        }
        $this->authorize('view', $user);
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

        $this->authorize('update', $user);

        $data = [
            'status' => $request->status,
            'ban_reason' => $request->ban_reason,
        ];

        $user = $this->userRepository->updateStatusUser($data, $user);

        return [
            'user' => new UserResource($user),
            'message' => $user->status === 'banned' ? 'User banned successfully' : 'User activated successfully',
            'code' => 200
        ];
    }
}
