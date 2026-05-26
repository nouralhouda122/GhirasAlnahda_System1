<?php

namespace App\Repositories;
use App\Http\Requests\campaign_kpiRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Models\EmailVerification;
use Carbon\Carbon;

class EmailVerficationRepository
{
    public function deleteByEmail(string $email): void
    {
        EmailVerification::where('email', $email)->delete();
    }

    public function create(string $email, string $code)
    {
        return EmailVerification::create([
            'email' => $email,
            'code'  => $code,
        ]);
    }
    public function exists(EmailVerificationRequest $request){
        return       EmailVerification::where('email', $request->email)
            ->where('code', $request->code)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->first();

    }


}
