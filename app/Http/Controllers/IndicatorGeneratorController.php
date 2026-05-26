<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IndicatorGeneratorService;

class IndicatorGeneratorController extends Controller
{
    public function __construct(
        private IndicatorGeneratorService $service
    ) {}

    public function generate(Request $request)
    {
        $analysis = $request->input('analysis');

        $result = $this->service->generate($analysis);

        return response()->json([
            'success' => true,
            'indicators' => $result
        ]);
    }
}
