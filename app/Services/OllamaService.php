<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
class OllamaService
{
    public function analyze(string $text)
    {
        set_time_limit(300);

        $response = Http::timeout(240)
            ->connectTimeout(10)
            ->post('http://127.0.0.1:11434/api/chat', [
                'model' => 'llama3:latest',
                'stream' => false,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Return ONLY JSON. No text. No markdown.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Return JSON only:
{
  \"domain\": \"volunteers|تبرعات\",
  \"intent\": \"growth|reduction\",
  \"target\": number,
  \"timeframe\": \"YYYY\"
}

Text: $text"
                    ]
                ]
            ]);

        // 🔥 أهم نقطة
        $data = $response->json();

        // لو Ollama فشل
        if (!$data) {
            return [
                "error" => "No response from Ollama"
            ];
        }

        // 👇 مهم جداً: chat API
        $raw = $data['message']['content'] ?? null;

        if (!$raw) {
            return [
                "error" => "Empty content from model",
                "debug" => $data
            ];
        }

        // تنظيف قوي
        $raw = trim($raw);
        $raw = preg_replace('/```json|```/', '', $raw);

        // استخراج JSON فقط
        preg_match('/\{.*\}/s', $raw, $matches);

        if (!isset($matches[0])) {
            return [
                "error" => "No JSON found",
                "raw" => $raw
            ];
        }

        $json = json_decode($matches[0], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                "error" => "Invalid JSON",
                "raw" => $matches[0]
            ];
        }

        return $json;
    }
}
