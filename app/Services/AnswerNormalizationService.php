<?php

namespace App\Services;

use App\Models\Question;

class AnswerNormalizationService
{
    public function normalize(mixed $answer, Question $question): float
    {
        return match ($question->type) {

            'rating' => $this->rating($answer, $question->scale),

            'yes_no' => $this->yesNo($answer),

            default => 0,
        };
    }

    private function rating(mixed $answer, ?int $scale): float
    {
        $scale = $scale ?: 5;

        return round(((float)$answer / $scale) * 100, 2);
    }

    private function yesNo(mixed $answer): float
    {
        return in_array(strtolower((string)$answer), ['yes', '1', 'true'])
            ? 100
            : 0;
    }
}
