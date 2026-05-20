<?php

namespace App\Services\Tests;

use App\Models\Test;

class LatexTestImportValidator
{
    private const VALID_TYPES = ['mcq', 'tf', 'numeric'];
    private const VALID_DIFFICULTIES = ['easy', 'medium', 'hard'];
    private const VALID_CONTENT = [
        'algebra',
        'advanced_math',
        'problem_solving_and_data_analysis',
        'geometry_and_trigonometry',
    ];

    public function validate(Test $test, array $parsed): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => $parsed['warnings'] ?? [],
            'module_summary' => [],
            'questions' => [],
        ];

        foreach (($parsed['errors'] ?? []) as $error) {
            $result['errors'][] = $error;
        }

        if ($this->testHasStudentAttempts($test)) {
            $result['errors'][] = 'Selected test already has student attempts and cannot be imported into.';
        }

        $incomingByModule = $this->countIncomingQuestionsByModule($parsed, $result['errors']);

        for ($moduleNumber = 1; $moduleNumber <= 5; $moduleNumber++) {
            $part = "part{$moduleNumber}";
            $expected = (int) ($test->{"{$part}_questions_count"} ?? 0);
            $existing = $this->existingQuestionsCount($test, $part);
            $incoming = $incomingByModule[$moduleNumber] ?? 0;
            $final = $existing + $incoming;

            $result['module_summary'][$moduleNumber] = [
                'part' => $part,
                'expected' => $expected,
                'existing' => $existing,
                'incoming' => $incoming,
                'final' => $final,
                'remaining_before_import' => max($expected - $existing, 0),
            ];

            if ($incoming > 0 && $expected <= 0) {
                $result['errors'][] = "Module {$moduleNumber} has incoming questions, but {$part}_questions_count is not configured.";
            }

            if ($expected > 0 && $final > $expected) {
                $result['errors'][] = "Module {$moduleNumber} would exceed {$part}_questions_count ({$final}/{$expected}).";
            }
        }

        foreach (($parsed['modules'] ?? []) as $module) {
            $moduleNumber = $this->asInteger($module['module_number'] ?? null);
            $part = $module['part'] ?? ($moduleNumber ? "part{$moduleNumber}" : null);
            $orderInModule = 1;

            if ($moduleNumber === null || $moduleNumber < 1 || $moduleNumber > 5) {
                $result['errors'][] = 'Invalid module number found during import validation. Module number must be between 1 and 5.';
                continue;
            }

            foreach (($module['questions'] ?? []) as $question) {
                $status = 'valid';
                $sourceIndex = $question['source_index'] ?? null;
                $questionErrorsBefore = count($result['errors']);

                $type = $this->normalizeToken($question['type'] ?? null);
                $difficulty = $this->normalizeToken($question['difficulty'] ?? null);
                $content = $this->normalizeToken($question['content'] ?? null);
                $text = $question['text'] ?? null;
                $choices = is_array($question['choices'] ?? null) ? $question['choices'] : [];
                $answer = $question['answer'] ?? null;

                $label = $sourceIndex ? "Question {$sourceIndex}" : "Question in module {$moduleNumber}";

                $this->validateRequiredQuestionFields($label, $type, $difficulty, $content, $text, $result['errors']);
                $this->validateQuestionTypePayload($label, $type, $choices, $answer, $result['errors']);

                $calculatedScore = null;
                if ($moduleNumber >= 1 && $moduleNumber <= 5 && in_array($difficulty, self::VALID_DIFFICULTIES, true)) {
                    $calculatedScore = $this->calculateScore($test, $moduleNumber, $difficulty);

                    if (!$this->isValidScore($calculatedScore)) {
                        $scoreField = "module{$moduleNumber}_{$difficulty}_score";
                        $result['errors'][] = "{$label}: calculated score from {$scoreField} is missing, zero, non-numeric, or outside 1..100.";
                    }
                }

                if (count($result['errors']) > $questionErrorsBefore) {
                    $status = 'invalid';
                }

                $result['questions'][] = [
                    'module_number' => $moduleNumber,
                    'part' => $part,
                    'source_index' => $sourceIndex,
                    'question_order' => $orderInModule,
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'content' => $content,
                    'calculated_score' => $calculatedScore,
                    'status' => $status,
                ];

                $orderInModule++;
            }
        }

        $result['valid'] = count($result['errors']) === 0;

        return $result;
    }

    private function validateRequiredQuestionFields(
        string $label,
        ?string $type,
        ?string $difficulty,
        ?string $content,
        mixed $text,
        array &$errors
    ): void {
        if ($type === null || $type === '') {
            $errors[] = "{$label}: missing type.";
        } elseif (!in_array($type, self::VALID_TYPES, true)) {
            $errors[] = "{$label}: invalid type '{$type}'. Accepted values are mcq, tf, numeric.";
        }

        if ($difficulty === null || $difficulty === '') {
            $errors[] = "{$label}: missing difficulty.";
        } elseif (!in_array($difficulty, self::VALID_DIFFICULTIES, true)) {
            $errors[] = "{$label}: invalid difficulty '{$difficulty}'. Accepted values are easy, medium, hard.";
        }

        if ($content === null || $content === '') {
            $errors[] = "{$label}: missing content.";
        } elseif (!in_array($content, self::VALID_CONTENT, true)) {
            $errors[] = "{$label}: invalid content '{$content}'.";
        }

        if (!is_string($text) || trim($text) === '') {
            $errors[] = "{$label}: missing text.";
        }
    }

    private function validateQuestionTypePayload(
        string $label,
        ?string $type,
        array $choices,
        mixed $answer,
        array &$errors
    ): void {
        if ($type === 'mcq') {
            if (count($choices) < 2) {
                $errors[] = "{$label}: MCQ questions must have at least 2 choices.";
            }

            if (count($choices) > 6) {
                $errors[] = "{$label}: MCQ questions must not have more than 6 choices.";
            }

            if (!$this->hasCorrectChoice($choices)) {
                $errors[] = "{$label}: MCQ questions must have at least one correct choice.";
            }
        }

        if (in_array($type, ['numeric', 'tf'], true) && (!is_string($answer) || trim($answer) === '')) {
            $errors[] = "{$label}: {$type} questions must have an answer.";
        }
    }

    private function countIncomingQuestionsByModule(array $parsed, array &$errors): array
    {
        $counts = [];

        foreach (($parsed['modules'] ?? []) as $module) {
            $moduleNumber = $this->asInteger($module['module_number'] ?? null);

            if ($moduleNumber === null || $moduleNumber < 1 || $moduleNumber > 5) {
                $errors[] = 'Invalid module number found while counting incoming questions.';
                continue;
            }

            $counts[$moduleNumber] = ($counts[$moduleNumber] ?? 0) + count($module['questions'] ?? []);
        }

        return $counts;
    }

    private function calculateScore(Test $test, int $moduleNumber, string $difficulty): mixed
    {
        $scoreField = "module{$moduleNumber}_{$difficulty}_score";

        return $test->{$scoreField} ?? null;
    }

    private function isValidScore(mixed $score): bool
    {
        if (!is_numeric($score)) {
            return false;
        }

        $score = (int) $score;

        return $score >= 1 && $score <= 100;
    }

    private function testHasStudentAttempts(Test $test): bool
    {
        if ($test->relationLoaded('studentTests')) {
            return $test->studentTests->isNotEmpty();
        }

        return $test->studentTests()->exists();
    }

    private function existingQuestionsCount(Test $test, string $part): int
    {
        if ($test->relationLoaded('questions')) {
            return $test->questions->where('part', $part)->count();
        }

        return $test->questions()->where('part', $part)->count();
    }

    private function hasCorrectChoice(array $choices): bool
    {
        foreach ($choices as $choice) {
            if (!empty($choice['is_correct'])) {
                return true;
            }
        }

        return false;
    }

    private function normalizeToken(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return strtolower(trim($value));
    }

    private function asInteger(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }

        return null;
    }
}
