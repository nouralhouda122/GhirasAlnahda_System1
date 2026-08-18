<?php

namespace App\Services;

use App\Repositories\GeneralDashboardDetailsRepository;
use Carbon\Carbon;

class GeneralDashboardDetailsService
{
    protected GeneralDashboardDetailsRepository $repository;

    public function __construct(
        GeneralDashboardDetailsRepository $repository
    ) {
        $this->repository = $repository;
    }


    /**
     * الحصول على تفاصيل نقطة معينة في الرسم البياني
     */
    public function getDetails(
        string $type,
        string $startDate,
        string $endDate
    ): array {

        /*
        |--------------------------------------------------------------------------
        | التحقق من نوع البيانات
        |--------------------------------------------------------------------------
        */

        $allowedTypes = [
            'campaigns',
            'volunteers',
            'donations',
        ];

        if (!in_array($type, $allowedTypes, true)) {

            return [
                'code' => 422,
                'data' => null,
                'message' => 'Invalid statistics type',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | تحويل التواريخ
        |--------------------------------------------------------------------------
        */

        try {

            $start = Carbon::parse($startDate)
                ->startOfDay();

            $end = Carbon::parse($endDate)
                ->endOfDay();

        } catch (\Exception $e) {

            return [
                'code' => 422,
                'data' => null,
                'message' => 'Invalid date format',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | التحقق من الفترة
        |--------------------------------------------------------------------------
        */

        if ($start->greaterThan($end)) {

            return [
                'code' => 422,
                'data' => null,
                'message' =>
                    'Start date must be before end date',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | جلب التفاصيل
        |--------------------------------------------------------------------------
        */

        $items = match ($type) {

            'campaigns' =>
            $this->repository->getCampaignDetails(
                $start,
                $end
            ),

            'volunteers' =>
            $this->repository->getVolunteerDetails(
                $start,
                $end
            ),

            'donations' =>
            $this->repository->getDonationDetails(
                $start,
                $end
            ),
        };


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return [
            'code' => 200,

            'data' => [

                'type' => $type,

                'period' => [
                    'start_date' =>
                        $start->format('Y-m-d'),

                    'end_date' =>
                        $end->format('Y-m-d'),
                ],

                'total' =>
                    $this->calculateTotal(
                        $type,
                        $items
                    ),

                'items' => $items,
            ],

            'message' =>
                'Dashboard statistics details retrieved successfully',
        ];
    }


    /**
     * حساب الإجمالي حسب نوع الإحصائية
     */
    private function calculateTotal(
        string $type,
        array $items
    ): int|float {

        /*
        |--------------------------------------------------------------------------
        | التبرعات
        |--------------------------------------------------------------------------
        */

        if ($type === 'donations') {

            return round(
                array_sum(
                    array_column(
                        $items,
                        'amount'
                    )
                ),
                2
            );
        }


        /*
        |--------------------------------------------------------------------------
        | الحملات والمتطوعين
        |--------------------------------------------------------------------------
        */

        return count($items);
    }
}
