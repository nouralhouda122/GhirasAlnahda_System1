<?php

namespace App\AI\Providers;

use App\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProviderInterface
{
    public function analyze(string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('openai.api_key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Return ONLY JSON: domain, intent, target'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0
        ]);

        $data = $response->json();

        $content = $data['choices'][0]['message']['content'] ?? null;

        return json_decode($content, true);
    }
}
