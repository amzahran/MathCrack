<?php

namespace App\Services\Tests;

use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Support\Facades\DB;

class TestQuestionScoreSyncer
{
    private const DIFFICULTIES = ['easy', 'medium', 'hard'];

    public function sync(Test $test): int
    {
        $scoresByModule = $this->scoresByModule($test);
        $updated = 0;

        TestQuestion::query()
            ->where('test_id', $test->id)
            ->select(['id', 'part', 'module_number', 'difficulty', 'score'])
            ->chunkById(200, function ($questions) use ($scoresByModule, &$updated) {
                foreach ($questions as $question) {
                    $moduleNumber = $this->resolveModuleNumber($question);
                    $difficulty = $this->normalizeDifficulty($question->difficulty);

                    if ($moduleNumber === null || $difficulty === null) {
                        continue;
                    }

                    $score = $scoresByModule[$moduleNumber][$difficulty] ?? null;

                    if ($score === null || (int) $question->score === $score) {
                        continue;
                    }

                    DB::table('test_questions')
                        ->where('id', $question->id)
                        ->update(['score' => $score]);

                    $updated++;
                }
            });

        return $updated;
    }

    private function scoresByModule(Test $test): array
    {
        $scores = [];

        for ($moduleNumber = 1; $moduleNumber <= 5; $moduleNumber++) {
            foreach (self::DIFFICULTIES as $difficulty) {
                $field = "module{$moduleNumber}_{$difficulty}_score";
                $scores[$moduleNumber][$difficulty] = (int) ($test->{$field} ?? 0);
            }
        }

        return $scores;
    }

    private function resolveModuleNumber(TestQuestion $question): ?int
    {
        $moduleNumber = (int) ($question->module_number ?? 0);

        if ($moduleNumber >= 1 && $moduleNumber <= 5) {
            return $moduleNumber;
        }

        if (is_string($question->part) && preg_match('/^part([1-5])$/', $question->part, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function normalizeDifficulty(?string $difficulty): ?string
    {
        $difficulty = strtolower(trim((string) $difficulty));

        return in_array($difficulty, self::DIFFICULTIES, true) ? $difficulty : null;
    }
}