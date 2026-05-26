<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndicatorMatchingService
{

    public function generate(array $aiResult, string $kpiText, int $goalId)
    {
        // ✅ تصحيح مهم (كان عندك خطأ هون)
        $domain = $aiResult['domain'] ?? 'general';

        // 🟢 1. جلب المؤشرات حسب المجال
        $indicators = DB::table('indicators')
            ->where('domain', $domain)
            ->orderBy('priority', 'desc')
            ->get();

        $kpiVector = $this->embed($kpiText);

        $results = [];

        foreach ($indicators as $indicator) {

            $indicatorVector = $this->embed(
                $indicator->name . ' ' . ($indicator->description ?? '')
            );

            $similarity = $this->cosineSimilarity($kpiVector, $indicatorVector);

            $dataAvailable = $this->checkDataAvailability($indicator);

            $feedbackWeight = $indicator->base_weight ?? 1;

            $score = $this->calculateScore(
                $similarity,
                $dataAvailable,
                $feedbackWeight
            );

            $results[] = [
                'id' => $indicator->id,
                'score' => $score,
            ];
        }

        // 🏆 2. ترتيب النتائج
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // 🔥 3. اختيار أفضل 3 مؤشرات فقط
        $topIndicators = array_slice($results, 0, 3);

        // 🔥 4. حفظ الربط (أهم خطوة)
        foreach ($topIndicators as $item) {
            DB::table('goal_indicator')->updateOrInsert(
                [
                    'campaign_kpi_id' => $goalId,
                    'indicator_id' => $item['id'],
                ],
                [
                    'score' => $item['score'], // ✅ حفظ الـ score
                ]
            );
        }

        return $topIndicators;
    }

    // =====================================================
    // 🧠 Embedding
    // =====================================================
    private function embed(string $text): array
    {
        $text = strtolower(substr($text, 0, 50));

        $vector = [];

        for ($i = 0; $i < strlen($text); $i++) {
            $vector[] = ord($text[$i]) % 100;
        }

        return $vector;
    }

    // =====================================================
    // 📊 Cosine Similarity
    // =====================================================
    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0;
        $magA = 0;
        $magB = 0;

        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $magA += $a[$i] ** 2;
            $magB += $b[$i] ** 2;
        }

        if ($magA == 0 || $magB == 0) return 0;

        return $dot / (sqrt($magA) * sqrt($magB));
    }

    // =====================================================
    // 🗄️ DB Check
    // =====================================================
    private function checkDataAvailability($indicator): bool
    {
        if (!$indicator->table_name) return false;

        if (!Schema::hasTable($indicator->table_name)) {
            return false;
        }

        return DB::table($indicator->table_name)->exists();
    }

    // =====================================================
    // ⚖️ Score
    // =====================================================
    private function calculateScore(
        float $similarity,
        bool $dataAvailable,
        float $feedbackWeight
    ): float {
        return (
            ($similarity * 0.5) +
            ($dataAvailable ? 0.3 : 0) +
            ($feedbackWeight * 0.2)
        );
    }
}
