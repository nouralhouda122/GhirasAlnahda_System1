<?php
namespace App\Repositories;
use App\Models\JoinRequest;
use App\Models\VolunteerProfile;
class VolunteerRequestRepository
{
    public function findById($id)
    {
        return JoinRequest::findOrFail($id);
    }
    public function updateStatus($id, $status)
    {
        $request = JoinRequest::findOrFail($id);
        $request->update(['status' => $status]);
        return $request;
    }
    public function createVolunteerProfile(array $data)
    {
        return VolunteerProfile::create($data);
    }
    public function getAllPending()
{
    return JoinRequest::with('user')
        ->where('status', 'pending')
        ->get();
}
}
