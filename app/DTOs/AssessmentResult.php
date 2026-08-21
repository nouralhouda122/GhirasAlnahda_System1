<?php

namespace App\DTOs;

class AssessmentResult
{
    public function __construct(
        public int $score,
        public string $status,
        public array $strengths = [],
        public array $weaknesses = [],
    ) {}
}
