<?php

namespace App\Services\Assignments;

use App\Models\LectureAssignment;

class LatexAssignmentImportValidator
{
    private const VALID_TYPES = ['mcq', 'numeric', 'tf', 'essay'];
    private const VALID_DIFFICULTIES = ['easy', 'medium', 'hard'];

    public function validate(LectureAssignment $assignment, array $parsed, ?array $archiveImages = null): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => $parsed['warnings'] ?? [],
            'questions' => [],
            'summary' => [
                'total_questions' => 0,
                'mcq' => 0,
                'numeric' => 0,
                'tf' => 0,
                'essay' => 0,
                'total_points' => 0,
            ],
        ];

        foreach (($parsed['errors'] ?? []) as $error) {
            $result['errors'][] = $error;
        }

        if ($this->assignmentHasStudentSubmissions($assignment)) {
            $result['errors'][] = 'Selected assignment already has student submissions and cannot be imported into.';
        }

        $questionOrder = 1;

        foreach (($parsed['questions'] ?? []) as $question) {
            $status = 'valid';
            $questionErrorsBefore = count($result['errors']);

            $sourceIndex = $question['source_index'] ?? $questionOrder;
            $label = "Question {$sourceIndex}";
            $type = $this->normalizeToken($question['type'] ?? null);
            $difficulty = $this->normalizeToken($question['difficulty'] ?? null);
            $text = $question['text'] ?? null;
            $answer = $question['answer'] ?? null;
            $choices = is_array($question['choices'] ?? null) ? $question['choices'] : [];
            $questionImageSource = $question['question_image_source'] ?? null;
            $explanationImageSource = $question['explanation_image_source'] ?? null;

            $this->validateRequiredQuestionFields($label, $type, $difficulty, $text, $result['errors']);
            $this->validateQuestionTypePayload($label, $type, $choices, $answer, $result['errors']);
            $this->validateImageReferences(
                $label,
                $questionImageSource,
                $explanationImageSource,
                $archiveImages,
                $result['errors']
            );

            $points = $this->resolvePoints($assignment, $label, $question['points'] ?? null, $difficulty, $result['errors']);

            if (count($result['errors']) > $questionErrorsBefore) {
                $status = 'invalid';
            }

            $result['questions'][] = [
                'source_index' => $sourceIndex,
                'type' => $type,
                'difficulty' => $difficulty,
                'points' => $points,
                'question_order' => $questionOrder,
                'status' => $status,
            ];

            $this->addToSummary($result['summary'], $type, $points);

            $questionOrder++;
        }

        $result['valid'] = count($result['errors']) === 0;

        return $result;
    }

    private function validateRequiredQuestionFields(
        string $label,
        ?string $type,
        ?string $difficulty,
        mixed $text,
        array &$errors
    ): void {
        if ($type === null || $type === '') {
            $errors[] = "{$label}: missing type.";
        } elseif (!in_array($type, self::VALID_TYPES, true)) {
            $errors[] = "{$label}: invalid type '{$type}'. Accepted values are mcq, numeric, tf, essay.";
        }

        if ($difficulty === null || $difficulty === '') {
            $errors[] = "{$label}: missing difficulty.";
        } elseif (!in_array($difficulty, self::VALID_DIFFICULTIES, true)) {
            $errors[] = "{$label}: invalid difficulty '{$difficulty}'. Accepted values are easy, medium, hard.";
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

        if ($type === 'numeric' && (!is_string($answer) || trim($answer) === '')) {
            $errors[] = "{$label}: numeric questions must have an answer.";
        }

        if ($type === 'tf' && !$this->isValidTrueFalseAnswer($answer)) {
            $errors[] = "{$label}: tf questions must have an answer of true, false, 1, or 0.";
        }
    }

    private function validateImageReferences(
        string $label,
        mixed $questionImageSource,
        mixed $explanationImageSource,
        ?array $archiveImages,
        array &$errors
    ): void {
        foreach ([$questionImageSource, $explanationImageSource] as $imageSource) {
            if ($imageSource === null || $imageSource === '') {
                continue;
            }

            if (!is_string($imageSource)) {
                $errors[] = "{$label}: unsupported image path.";
                continue;
            }

            $imagePath = trim($imageSource);

            if (!$this->isSafeRelativeImagePath($imagePath)) {
                $errors[] = "{$label}: unsupported image path: {$imagePath}";
                continue;
            }

            if ($archiveImages !== null && !array_key_exists($imagePath, $archiveImages)) {
                $errors[] = "{$label}: image not found: {$imagePath}";
            }
        }
    }

    private function resolvePoints(
        LectureAssignment $assignment,
        string $label,
        mixed $points,
        ?string $difficulty,
        array &$errors
    ): int {
        if ($points !== null && $points !== '') {
            if (!$this->isIntegerValue($points)) {
                $errors[] = "{$label}: points must be an integer between 0 and 100.";

                return 0;
            }

            $points = (int) $points;

            if ($points < 0 || $points > 100) {
                $errors[] = "{$label}: points must be between 0 and 100.";

                return $points;
            }

            return $points;
        }

        return $this->assignmentDefaultPoints($assignment, $difficulty);
    }

    private function assignmentDefaultPoints(LectureAssignment $assignment, ?string $difficulty): int
    {
        $fallbacks = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
        ];

        $difficulty = in_array($difficulty, self::VALID_DIFFICULTIES, true) ? $difficulty : 'medium';
        $field = "{$difficulty}_points";
        $value = $assignment->{$field} ?? null;

        if ($value === null || $value === '' || !is_numeric($value)) {
            return $fallbacks[$difficulty];
        }

        return (int) $value;
    }

    private function addToSummary(array &$summary, ?string $type, int $points): void
    {
        $summary['total_questions']++;
        $summary['total_points'] += $points;

        if (in_array($type, self::VALID_TYPES, true)) {
            $summary[$type]++;
        }
    }

    private function assignmentHasStudentSubmissions(LectureAssignment $assignment): bool
    {
        if ($assignment->relationLoaded('studentAssignments')) {
            return $assignment->studentAssignments->isNotEmpty();
        }

        return $assignment->studentAssignments()->exists();
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

    private function isValidTrueFalseAnswer(mixed $answer): bool
    {
        if (!is_string($answer) && !is_numeric($answer) && !is_bool($answer)) {
            return false;
        }

        $answer = strtolower(trim((string) $answer));

        return in_array($answer, ['true', 'false', '1', '0'], true);
    }

    private function isIntegerValue(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (is_string($value)) {
            return preg_match('/^-?\d+$/', trim($value)) === 1;
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

    private function isSafeRelativeImagePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if (str_contains($path, '\\') || str_contains($path, '..')) {
            return false;
        }

        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:\//', $path) === 1) {
            return false;
        }

        if (preg_match('/^(?:[a-z][a-z0-9+.-]*:|\/\/)/i', $path) === 1) {
            return false;
        }

        return true;
    }
}
