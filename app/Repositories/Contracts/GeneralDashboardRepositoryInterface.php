<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;

interface GeneralDashboardRepositoryInterface
{
    /**
     * Get total donations for a specific period.
     */
    public function getTotalDonations(
        Carbon $start,
        Carbon $end
    ): float;

    /**
     * Get number of volunteers created during a specific period.
     */
    public function getTotalVolunteers(
        Carbon $start,
        Carbon $end
    ): int;

    /**
     * Get number of currently active campaigns.
     */
    public function getActiveCampaigns(): int;

    /**
     * Get number of completed campaigns during a specific period.
     */
    public function getCompletedCampaigns(
        Carbon $start,
        Carbon $end
    ): int;

    /**
     * Get number of active campaigns created during a specific period.
     */
    public function getActiveCampaignsCreatedBetween(
        Carbon $start,
        Carbon $end
    ): int;
}
