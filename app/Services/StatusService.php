<?php

namespace App\Services;

class StatusService
{
    public function getStatus(float $score): string
    {
        return match (true) {

            $score >= 80 => 'excellent',

            $score >= 60 => 'good',

            $score >= 40 => 'warning',

            default => 'critical',
        };
    }
}
