<?php

namespace App\AI\Providers;

use App\AI\AIProviderInterface;

class MockAIProvider implements AIProviderInterface
{
    public function analyze(string $prompt): array
    {
        return [
            "domain" => "volunteers",
            "intent" => "growth",
            "target" => 30
        ];
    }
}
