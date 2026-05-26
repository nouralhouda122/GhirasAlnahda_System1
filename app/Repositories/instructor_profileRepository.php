<?php

namespace App\Repositories;

use App\Models\instructor_profile;

class instructor_profileRepository
{
    public function create(array $data)
    {
        return instructor_profile::create([
            'user_id' => $data['user_id'],
            'bio' => $data['bio'] ?? null,
        ]);
    }

    public function update(int $userId, array $data)
    {
        return instructor_profile::where(
            'user_id',
            $userId
        )->update([
            'bio' => $data['bio']
        ]);
    }

    public function findByUserId(int $userId)
    {
        return instructor_profile::where(
            'user_id',
            $userId
        )->first();
    }
}
