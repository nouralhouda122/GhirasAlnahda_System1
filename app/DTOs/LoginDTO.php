<?php

namespace App\DTOs;

use App\Http\Requests\LoginRequest;

class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password')
        );
    }
}


