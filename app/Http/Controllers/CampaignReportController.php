<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\CampaignReportService;

class CampaignReportController extends Controller
{
    public function __construct(
        private CampaignReportService $reportService
    ) {}

    /**
     * عرض التقرير الكامل للحملة
     */
    public function show(int $campaignId)
    {
        $result =
            $this->reportService
                ->generateReport(
                    $campaignId
                );

        if ($result['code'] === 200) {

            return ResponseHelper::Success(
                $result['data'],
                $result['message'],
                $result['code']
            );
        }

        return ResponseHelper::Error(
            [],
            $result['message'],
            $result['code']
        );
    }
}
