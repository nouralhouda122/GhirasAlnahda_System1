<?php

namespace App\AI;

interface AIProviderInterface
{
    public function analyze(string $prompt): array;
}
