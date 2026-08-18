<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\GeneralDashboardDetailsRequest;
use App\Services\GeneralDashboardDetailsService;

class GeneralDashboardDetailsController extends Controller
{
    protected GeneralDashboardDetailsService $service;

    public function __construct(
        GeneralDashboardDetailsService $service
    ) {
        $this->service = $service;
    }

    /**
     * عرض تفاصيل نقطة معينة في الرسم البياني
     */
    public function getDetails(
        GeneralDashboardDetailsRequest $request
    ) {
        $validated = $request->validated();

        $result = $this->service->getDetails(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date']
        );

        return ResponseHelper::Success(
            $result['data'],
            $result['message'],
            $result['code']
        );
    }}
