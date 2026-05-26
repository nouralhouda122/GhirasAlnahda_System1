<?php

namespace App\Http\Controllers;

use App\Services\KPIBrain;
use Illuminate\Http\Request;
use App\Services\GoalPipelineService;

class KPIController extends Controller
{
public function analyze(Request $request, KPIBrain $brain)
{
    $request->validate([
        'goal' => 'required|string'
    ]);
    $result = $brain->analyze($request->goal);
    return response()->json([
        "status" => "analyzed",
        "input" => $request->goal,
        "result" => $result
    ]);
}}
