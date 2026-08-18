<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\GeneralDashboardService;
use Illuminate\Http\Request;

class GeneralDashboardController extends Controller
{
    protected $GeneralDashboardService;

    public function __construct(
        GeneralDashboardService $GeneralDashboardService
    ) {
        $this->GeneralDashboardService =
            $GeneralDashboardService;
    }
    /**
     * Dashboard Overview
     */
    public function overview()
    {
        $data =
            $this->GeneralDashboardService
                ->getOverview();

        return ResponseHelper::Success(
            $data,
            'Dashboard overview retrieved successfully',
            200
        );
    }

    /**
     * Dashboard KPIs
     */
    public function kpis(Request $request)
    {
        $period = $request->query(
            'period',
            'monthly'
        );

        $data =
            $this->GeneralDashboardService
                ->getKpis($period);


        return ResponseHelper::Success(
            $data,
            'Dashboard KPIs retrieved successfully',
            200
        );
    }/**
 * Dashboard detailed statistics
 */
    public function statistics(Request $request)
    {
        $period = $request->query(
            'period',
            'monthly'
        );

        $data =
            $this->GeneralDashboardService
                ->getStatistics($period);


        return ResponseHelper::Success(
            $data,
            'Dashboard statistics retrieved successfully',
            200
        );
    }
}
