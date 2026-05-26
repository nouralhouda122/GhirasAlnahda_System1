<?php


namespace App\Http\Controllers;



use App\Services\IndicatorEngine;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    protected $engine;

    public function __construct(IndicatorEngine $engine)
    {
        $this->engine = $engine;
    }

    public function generate(Request $request)
    {
        $analysis = $request->input('analysis');

        $result = $this->engine->generate($analysis);

        return response()->json($result);
    }

}
