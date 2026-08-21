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
        $campaignId
    ) {
        // Route parameters come as strings
        $campaignId = (int) $campaignId;

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


    /**
     * Export Campaign Report as Excel
     *
     * This generates an HTML table which Microsoft Excel
     * can open as an XLS file.
     */
/**
     * Export Campaign Report as Excel
     */
/**
     * Export Campaign Report as Excel
     */
    public function excel(
        Request $request,
        $campaignId
    ) {
        $campaignId = (int) $campaignId;

        $result = $this->reportService->generateReport($campaignId);

        if ($result['code'] !== 200) {
            return response()->json([
                'status' => 0,
                'message' => $result['message'],
            ], $result['code']);
        }

        $report = $result['data'];

        $filename = 'campaign-report-' . $campaignId . '.xls';

        // جلب الـ HTML من ملف الـ Blade
        $html = view('reports.campaign-excel', [
            'report' => $report,
        ])->render();

        // إرجاع الملف مع الهيدر الصحيح لدعم اللغة العربية وتفعيل التحميل الفوري
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
        }}