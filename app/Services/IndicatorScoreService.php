<?php

namespace App\Services;

use App\Models\Indicator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndicatorScoreService
{
    public function __construct(
        private SurveyScoringService $surveyService
    ) {}

    public function calculate(
        Indicator $indicator,
        int $campaignId
    ): float {

        return match ($indicator->data_source) {

            'survey' => $this->surveyService
                ->calculateIndicatorScore($indicator, $campaignId),

            'database' => $this->databaseScore($indicator, $campaignId),

            default => 0,
        };
    }

    private function databaseScore(
        Indicator $indicator,
        int $campaignId
    ): float {
        if (
            !$indicator->table_name ||
            !$indicator->column_name
        ) {
            return 0;
        }
        if (!Schema::hasTable($indicator->table_name)) {
            return 0;
        }
        if (!Schema::hasColumn($indicator->table_name, $indicator->column_name)) {
            return 0;
        }
        $query = DB::table($indicator->table_name);
        if (Schema::hasColumn($indicator->table_name, 'campaign_id')) {
            $query->where('campaign_id', $campaignId);
        }
        $value = match ($indicator->calculation_type) {
            'count' => $query->count(),
            'sum' => $query->sum($indicator->column_name),
            'avg' => $query->avg($indicator->column_name),
            'percentage' => $query->avg($indicator->column_name),

            default => 0,
        };

        if (!$value) return 0;

        // normalization rule
        return min($value, 100);
    }
}
