<?php
namespace App\Services;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Repositories\ApprovalRequestRepository;
use App\Repositories\CourseRepository;
use App\Repositories\CourseScheduleRepository;
use App\Repositories\CourseSkillRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\instructor_profileRepository;
use App\Repositories\SpecializationRepository;
use App\Repositories\userRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\Cache\Adapter\save;

class CourseService
{
    public function __construct(
        EnrollmentRepository $EnrollmentRepository,
        ApprovalRequestRepository $approvalRequestRepository,

        CourseRepository $courseRepository,
        CourseSkillRepository $skillRepository,
        CourseScheduleRepository $scheduleRepository,
        UserRepository $userRepository,
        instructor_profileRepository $profileRepository,
        SpecializationRepository $specializationRepository
    ){
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $EnrollmentRepository;
        $this->skillRepository = $skillRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->specializationRepository = $specializationRepository;
        $this->approvalRequestRepository = $approvalRequestRepository;

    }
    public function create(CourseRequest $request)
    {
        return DB::transaction(function () use ($request) {
            if ($request->filled('instructor')) {
                $instructor = $this->userRepository->create_instructor([
                    'name' => $request->instructor['name'],
                    'email' => $request->instructor['email'],
                    'phone' => $request->instructor['phone'],
                ]);
                $this->profileRepository->create([
                    'user_id' => $instructor->id,
                    'bio' => $request->instructor['bio'],
                ]);
                foreach ($request->instructor['specializations'] ?? [] as $name) {
                    $spec = $this->specializationRepository->findOrCreate($name);
                    $instructor->specializations()->syncWithoutDetaching([$spec->id]);
                }
            }

            $Course = $this->courseRepository->create($request->toArray(),$instructor->id);
            foreach ($request->skills ?? [] as $skill) {
                $this->skillRepository->create([
                    'skill_name' => $skill,
                    'course_id' => $Course->id,
                ]);
            }
            if ($request->filled('schedule')) {
                foreach ($request->schedule ?? [] as $schedule) {
                    $this->scheduleRepository->create([
                        'course_id' => $Course->id,
                        'from_time' => $schedule['from_time'],
                        'to_time' => $schedule['to_time'],
                        'day' => $schedule['day'],
                    ]);
                }
            }
            $Course->load('instructor','skills','schedules');
            $this->approvalRequestRepository->create([
                'type'=>'course',
                'status'=>'pending',
                'approvable_id'=>$Course->id,
                'approvable_type' => 'course',
                'requested_by'=>Auth::user()->id
            ]);
            return [
                'user' =>new CourseResource($Course) ,
                'message' => 'Course created successfully',
                'code' => 201
            ];

        });}

    public function index()
    {
        $Course = $this->courseRepository->indexAll();

        if ($Course->isEmpty()) {
            return [
                'user' => [],
                'message' => 'No courses found.',
                'code' => 200
            ];
        }

        return [
            'user' => CourseResource::collection($Course),
            'message' => 'Courses retrieved successfully',
            'code' => 200
        ];
    }
    public function show($id)
    {
        $Course = $this->courseRepository->findById($id);
        if (!$Course) {
            return [
                'user' => null,
                'message' => 'Course not found.',
                'code' => 404
            ];
        }
        return [
            'user' => new CourseResource($Course),
            'message' => 'Course retrieved successfully',
            'code' => 200
        ];
    }
    public function store($id)
    {
        $user = Auth::user();

        $course = $this->courseRepository->findById($id);

        if (!$course) {
            return [
                'user' => null,
                'message' => 'Course not found.',
                'code' => 404
            ];
        }

        if (!$user->hasRole('Volunteer')) {
            return [
                'user' => null,
                'message' => 'Only volunteers are allowed to enroll in courses.',
                'code' => 403
            ];
        }

        if ($this->enrollmentRepository->alreadyEnrolled($user->id, $course->id)) {
            return [
                'user' => null,
                'message' => 'You are already enrolled in this course.',
                'code' => 409
            ];
        }

        if ($user->volunteerProfile->pointsBalance < $course->required_points) {
            return [
                'user' => null,
                'message' => 'Insufficient points. You need ' . $course->required_points . ' points to enroll.',
                'code' => 403
            ];
        }

        $user->volunteerProfile->pointsBalance -= $course->required_points;
        $user->volunteerProfile->save();

        $enrollment = $this->enrollmentRepository->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active'
        ]);

        return [
            'user' => $enrollment,
            'message' => 'Successfully enrolled in the course.',
            'code' => 201
        ];
    }

    public function showMyCourses()
    {
       $user=Auth::user();
       $myCourses=$user->myCourses()->get();
        return [
            'user' => CourseResource::collection($myCourses),
            'message' => 'User courses retrieved successfully.',
            'code' => 200
        ];
    }
}
