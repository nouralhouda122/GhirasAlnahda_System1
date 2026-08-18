<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\VolunteerProfile;
use Carbon\Carbon;

class GeneralDashboardDetailsRepository
{
    /**
     * تفاصيل الحملات خلال فترة معينة
     */
    public function getCampaignDetails(
        Carbon $start,
        Carbon $end
    ): array {
        return Campaign::query()
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get([
                'id',
                'title',
                'status',
                'type',
                'location',
                'start_date',
                'end_date',
                'current_amount',
                'target_amount',
                'current_volunteers',
                'required_volunteers',
                'created_at',
            ])
            ->map(fn (Campaign $campaign) => [
                'id' => (int) $campaign->id,

                'title' => $campaign->title,

                'status' => $campaign->status,

                'type' => $campaign->type,

                'location' => $campaign->location,

                'start_date' => $campaign->start_date,

                'end_date' => $campaign->end_date,

                'donations' => (float) $campaign->current_amount,

                'target_amount' => (float) $campaign->target_amount,

                'volunteers' => [
                    'current' => (int) $campaign->current_volunteers,
                    'required' => (int) $campaign->required_volunteers,
                ],

                'created_at' => $campaign->created_at?->format('Y-m-d'),
            ])
            ->values()
            ->toArray();
    }


    /**
     * تفاصيل المتطوعين خلال فترة معينة
     *
     * لا نحتاج جدول متبرعين هنا.
     */
    public function getVolunteerDetails(
        Carbon $start,
        Carbon $end
    ): array {

        return VolunteerProfile::query()
            ->with([
                'user:id,name,email,phone',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get([
                'id',
                'user_id',
                'age',
                'gender',
                'current_address',
                'preferred_sector',
                'preferred_field',
                'weekly_hours_capacity',
                'totalHours',
                'pointsBalance',
                'isTeamLeader',
                'created_at',
            ])
            ->map(function (VolunteerProfile $volunteer) {

                return [
                    'id' => (int) $volunteer->id,

                    'user' => [
                        'id' => (int) $volunteer->user->id,
                        'name' => $volunteer->user->name,
                        'email' => $volunteer->user->email,
                        'phone' => $volunteer->user->phone,
                    ],

                    'age' => (int) $volunteer->age,

                    'gender' => $volunteer->gender,

                    'current_address' =>
                        $volunteer->current_address,

                    'preferred_sector' =>
                        $volunteer->preferred_sector,

                    'preferred_field' =>
                        $volunteer->preferred_field,

                    'weekly_hours_capacity' =>
                        $volunteer->weekly_hours_capacity,

                    'total_hours' =>
                        (int) $volunteer->totalHours,

                    'points_balance' =>
                        (int) $volunteer->pointsBalance,

                    'is_team_leader' =>
                        (bool) $volunteer->isTeamLeader,

                    'created_at' =>
                        $volunteer->created_at?->format('Y-m-d'),
                ];
            })
            ->values()
            ->toArray();
    }    /**
     * تفاصيل التبرعات خلال فترة معينة.
     *
     * لا يوجد جدول مستقل للمتبرعين/التبرعات.
     *
     * لذلك نعتمد على current_amount الموجود
     * في campaigns.
     */
    public function getDonationDetails(
        Carbon $start,
        Carbon $end
    ): array {
        return Campaign::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('current_amount', '>', 0)
            ->orderBy('created_at')
            ->get([
                'id',
                'title',
                'current_amount',
                'target_amount',
                'created_at',
            ])
            ->map(fn (Campaign $campaign) => [
                'campaign_id' => (int) $campaign->id,

                'campaign_title' => $campaign->title,

                'amount' => (float) $campaign->current_amount,

                'target_amount' =>
                    (float) $campaign->target_amount,

                'date' =>
                    $campaign->created_at?->format('Y-m-d'),
            ])
            ->values()
            ->toArray();
    }
}
