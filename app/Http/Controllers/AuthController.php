<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request) {
        $data = $this->userService->register($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function verify(EmailVerificationRequest $request)
    {
        $data = $this->userService->Verify($request);
        return ($data['code'] === 200)
            ? ResponseHelper::Success($data['user'], $data['message'], 200)
            : ResponseHelper::Error($data['user'], $data['message'], $data['code']);
    }
    public function login(LoginRequest $request)
    {
        $data = $this->userService->login($request);

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function logout()
    {
        $data = [];
        try {
            $data = $this->userService->logout();
            return ResponseHelper::Success([], $data['message'], 200);
        } catch (\Exception $e) {
            return ResponseHelper::Error(null, "Unexpected error: " . $e->getMessage(), 500);
        }
    }
}

