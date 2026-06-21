<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TeamRequestService;

class TeamRequestController extends Controller
{
    public function __construct(private TeamRequestService $service) {}

    public function create(Request $request)
    {
        return response()->json(
            $this->service->createTeamRequest(
                $request->campaign_id,
                $request->member_ids
            )
        );
    }

    public function accept($id)
    {
        return response()->json($this->service->acceptInvitation($id));
    }

    public function reject($id)
    {
        return response()->json($this->service->rejectInvitation($id));
    }
}