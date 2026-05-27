<?php

namespace App\Services;

class KPIBrain
{

    public function analyze(string $goal): array
    {
        $text = $this->normalize($goal);

        $domainScore = $this->scoreDomains($text);
        $intentScore = $this->scoreIntent($text);

        $domain = $this->pickTop($domainScore);
        $intent = $this->pickTop($intentScore);

        $target = $this->extractTarget($text);

        $type = $this->detectType($domain, $text);

        $confidence = $this->confidence($domainScore, $intentScore, $target);

        return [
            'domain' => $domain,
            'intent' => $intent,
            'type' => $type,
            'target_value' => $target,
            'confidence' => $confidence,
        ];
    }

    // =====================================================
    // 🔤 NORMALIZATION
    // =====================================================
    private function normalize(string $text): string
    {
        $map = [
            'زيادة' => 'increase',
            'رفع' => 'increase',
            'تحسين' => 'improve',
            'تطوير' => 'improve',
            'تحقيق' => 'achieve',
            'تقليل' => 'reduce',
            'خفض' => 'reduce',

            'متطوعين' => 'volunteers',
            'متطوع' => 'volunteers',
            'مشاركين' => 'participants',

            'رضا' => 'satisfaction',
            'جودة' => 'quality',
            'تقييم' => 'quality',

            'حضور' => 'attendance',
            'غياب' => 'absence',

            'تبرعات' => 'donation',
            'تبرع' => 'donation',

            'نقاط' => 'points',
        ];

        $text = mb_strtolower($text);
        $text = str_replace(array_keys($map), array_values($map), $text);

        $text = preg_replace('/[^\p{L}\p{N}\s%]/u', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    // =====================================================
    // 🌍 DOMAIN SCORING (IMPORTANT FIX)
    // =====================================================
    private function scoreDomains(string $text): array
    {
        $domains = [
            'volunteers' => [
                'volunteers' => 3,
                'participants' => 2,
            ],
            'quality' => [
                'satisfaction' => 3,
                'quality' => 2,
                'experience' => 2,
            ],
            'attendance' => [
                'attendance' => 3,
                'absence' => 2,
            ],
            'donations' => [
                'donation' => 3,
            ],
            'points' => [
                'points' => 3,
            ],
            'education' => [
                'training' => 2,
                'education' => 2,
            ],
        ];

        $scores = [];

        foreach ($domains as $domain => $words) {
            $scores[$domain] = 0;

            foreach ($words as $word => $weight) {
                if (str_contains($text, $word)) {
                    $scores[$domain] += $weight;
                }
            }
        }

        return $scores;
    }

    // =====================================================
    // 🎯 INTENT SCORING
    // =====================================================
    private function scoreIntent(string $text): array
    {
        $intents = [
            'growth' => ['increase'],
            'improvement' => ['improve', 'achieve'],
            'reduction' => ['reduce'],
        ];

        $scores = [];

        foreach ($intents as $intent => $words) {
            $scores[$intent] = 0;

            foreach ($words as $word) {
                if (str_contains($text, $word)) {
                    $scores[$intent]++;
                }
            }
        }

        return $scores;
    }

    // =====================================================
    // 🏆 PICK TOP VALUE
    // =====================================================
    private function pickTop(array $scores): string
    {
        arsort($scores);

        $topKey = array_key_first($scores);

        return $scores[$topKey] > 0 ? $topKey : 'general';
    }

    // =====================================================
    // 📊 TYPE DETECTION FIXED
    // =====================================================
    private function detectType(string $domain, string $text): string
    {
        if (in_array($domain, ['quality', 'education'])) {
            return 'qualitative';
        }

        if (preg_match('/\d+/', $text)) {
            return 'quantitative';
        }

        return 'qualitative';
    }

    // =====================================================
    // 🔢 TARGET EXTRACTION (IMPROVED)
    // =====================================================
    private function extractTarget(string $text): ?int
    {
        if (preg_match('/(\d+)\s*%/', $text, $m)) {
            return (int) $m[1];
        }

        if (preg_match('/\b\d+\b/', $text, $m)) {
            return (int) $m[0];
        }

        return null;
    }

    // =====================================================
    // 🎯 CONFIDENCE ENGINE
    // =====================================================
    private function confidence(array $domain, array $intent, ?int $target): float
    {
        $score = 0;

        $maxDomain = max($domain);
        $maxIntent = max($intent);

        if ($maxDomain > 0) $score += 0.4;
        if ($maxIntent > 0) $score += 0.3;
        if ($target !== null) $score += 0.3;

        return round($score, 2);
    }
}
