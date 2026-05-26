<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class GoalController
{
    public function show($id)
    {
        // ✅ البحث عن الهدف فقط
        $goal = \App\Models\Campaign_kpi::find($id);

        // ❌ إذا لم يوجد الهدف
        if (!$goal) {
            return response()->json([
                'status' => 0,
                'data' => null,
                'message' => 'Goal not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // ✅ جلب المؤشرات المرتبطة مع الـ score
        $indicators = DB::table('goal_indicator')
            ->join('indicators', 'goal_indicator.indicator_id', '=', 'indicators.id')
            ->where('goal_indicator.campaign_kpi_id', $goal->id)
            ->select(
                'indicators.id',
                'indicators.name',
                'indicators.type',
                'indicators.domain',
                'goal_indicator.score'
            )
            ->get();

        // ✅ إذا لم يوجد مؤشرات
        if ($indicators->isEmpty()) {
            return response()->json([
                'status' => 1,
                'data' => [
                    'goal' => $goal->goal_text,
                    'indicators' => []
                ],
                'message' => 'Goal retrieved successfully, but no indicators found'
            ], Response::HTTP_OK);
        }

        // ✅ الرد النهائي
        return response()->json([
            'status' => 1,
            'data' => [
                'goal' => $goal->goal_text,
                'indicators' => $indicators->map(function ($indicator) {
                    return [
                        'id' => $indicator->id,
                        'name' => $indicator->name,
                     //   'type' => $indicator->type,
                      //  'domain' => $indicator->domain,
                        'score' => (float) ($indicator->score ?? 0)
                    ];
                })
            ],
            'message' => 'Goal retrieved successfully'
        ], Response::HTTP_OK);
    }
}
