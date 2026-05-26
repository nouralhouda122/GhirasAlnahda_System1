<?php


namespace App\Repositories;


use App\Models\Attendance;
use App\Models\Campaign;

class AttendanceRepository
{
    public function create(array $data)
    {
        return Attendance::create($data);
    }
    public function findActiveLeaderSession($userId, $campaignId)
    {
        return Attendance::where('volunteer_id', $userId)
            ->where('campaign_id', $campaignId)
            ->whereNull('check_out_time')
            ->first();
    }
    public function update(array $data,$attendance)
    {
        $attendance->update(
            $data
        );
    }
}
