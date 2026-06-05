<?php
namespace App\Repositories;


use App\Models\Indicator;
use Illuminate\Support\Facades\DB;

class IndicatorRepository
{
    public function getById($id )

    {
        return Indicator::find($id);
    }
    public function isLinkedToCampaign(int $campaignId, int $indicatorId): bool
    {
        return DB::table('goal_indicators')
            // التصحيح: نربط جدول goal_indicators مع جدول campaign_kpis عبر حقل campaign_kpi_id المشترك
            ->join('campaign_kpis', 'goal_indicators.campaign_kpi_id', '=', 'campaign_kpis.id')
            // الفلترة: نختار السجلات التي تخص رقم الحملة الحالية ورقم المؤشر المطلوب
            ->where('campaign_kpis.campaign_id', $campaignId)
            ->where('goal_indicators.indicator_id', $indicatorId)
            ->exists();
    }}
