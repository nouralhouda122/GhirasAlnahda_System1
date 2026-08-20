<?php

namespace App\Http\Controllers;

use App\Services\CampaignReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CampaignReportExportController extends Controller
{
    public function __construct(
        private CampaignReportService $reportService
    ) {}

    /**
     * Export Campaign Report as PDF
     */
    public function pdf(
        Request $request,
        int $campaignId
    ) {
        // Generate the same report used by the API
        $result = $this->reportService->generateReport(
            $campaignId
        );

        // Campaign not found / report error
        if ($result['code'] !== 200) {
            return response()->json([
                'status' => 0,
                'message' => $result['message'],
            ], $result['code']);
        }

        // Generate PDF from Blade view
        $pdf = Pdf::loadView(
            'reports.campaign',
            [
                'report' => $result['data'],
            ]
        );

        // A4 paper
        $pdf->setPaper('A4', 'portrait');

        // Download PDF
        return $pdf->download(
            'campaign-report-' . $campaignId . '.pdf'
        );
    }
}
