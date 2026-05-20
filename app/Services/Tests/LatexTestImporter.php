<?php

namespace App\Services\Tests;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class LatexTestImporter
{
    /**
     * Import validated LaTeX parser output into an existing test.
     *
     * The $payload should contain parsed modules/questions. If validator output is
     * passed in the same array, calculated scores are read from payload['questions'].
     */
    public function import(Test $test, array $payload): array
    {
        return DB::transaction(function () use ($test, $payload): array {
            if ($this->testHasStudentAttempts($test)) {
                throw new RuntimeException('Selected test already has student attempts and cannot be imported into.');
            }

            $scoreMap = $this->buildScoreMap($payload['questions'] ?? []);
            $orderByPart = $this->getStartingOrdersByPart($test);

            $importedQuestions = 0;
            $importedOptions = 0;
            $modules = [];

            foreach (($payload['modules'] ?? []) as $module) {
                $moduleNumber = $this->requiredInt($module['module_number'] ?? null, 'module_number');
                $part = $module['part'] ?? "part{$moduleNumber}";

                foreach (($module['questions'] ?? []) as $questionData) {
                    $sourceIndex = $this->requiredInt($questionData['source_index'] ?? null, 'source_index');
                    $type = $this->requiredString($questionData['type'] ?? null, "Question {$sourceIndex} type");
                    $difficulty = $this->requiredString($questionData['difficulty'] ?? null, "Question {$sourceIndex} difficulty");
                    $content = $this->requiredString($questionData['content'] ?? null, "Question {$sourceIndex} content");
                    $questionText = $this->requiredString($questionData['text'] ?? null, "Question {$sourceIndex} text");
                    $score = $this->resolveCalculatedScore($questionData, $scoreMap, $sourceIndex);

                    $orderByPart[$part] = ($orderByPart[$part] ?? $this->getMaxQuestionOrder($test, $part)) + 1;

                    $question = TestQuestion::create([
                        'test_id' => $test->id,
                        'question_text' => $questionText,
                        'explanation' => $questionData['explanation'] ?? null,
                        'explanation_image' => null,
                        'question_image' => null,
                        'type' => $type,
                        'part' => $part,
                        'module_number' => $moduleNumber,
                        'question_order' => $orderByPart[$part],
                        'score' => $score,
                        'difficulty' => $difficulty,
                        'content' => $content,
                        'correct_answer' => $type === 'mcq' ? '' : $this->requiredString(
                            $questionData['answer'] ?? null,
                            "Question {$sourceIndex} answer"
                        ),
                    ]);

                    $importedQuestions++;
                    $modules[$moduleNumber] = ($modules[$moduleNumber] ?? 0) + 1;

                    if ($type === 'mcq') {
                        foreach (($questionData['choices'] ?? []) as $index => $choice) {
                            TestQuestionOption::create([
                                'test_question_id' => $question->id,
                                'option_text' => $this->requiredString(
                                    $choice['text'] ?? null,
                                    "Question {$sourceIndex} choice " . ($index + 1)
                                ),
                                'option_image' => null,
                                'is_correct' => !empty($choice['is_correct']),
                                'option_order' => $index + 1,
                            ]);

                            $importedOptions++;
                        }
                    }
                }
            }

            return [
                'imported_questions' => $importedQuestions,
                'imported_options' => $importedOptions,
                'test_id' => $test->id,
                'modules' => $modules,
            ];
        });
    }

    private function buildScoreMap(array $validatedQuestions): array
    {
        $map = [];

        foreach ($validatedQuestions as $question) {
            if (isset($question['source_index'], $question['calculated_score'])) {
                $map[(int) $question['source_index']] = $question['calculated_score'];
            }
        }

        return $map;
    }

    private function resolveCalculatedScore(array $questionData, array $scoreMap, int $sourceIndex): int
    {
        $score = $questionData['calculated_score'] ?? ($scoreMap[$sourceIndex] ?? null);

        if (!is_numeric($score)) {
            throw new InvalidArgumentException("Question {$sourceIndex} is missing calculated_score.");
        }

        $score = (int) $score;

        if ($score < 1 || $score > 100) {
            throw new InvalidArgumentException("Question {$sourceIndex} has invalid calculated_score {$score}.");
        }

        return $score;
    }

    private function getStartingOrdersByPart(Test $test): array
    {
        $orders = [];

        for ($moduleNumber = 1; $moduleNumber <= 5; $moduleNumber++) {
            $part = "part{$moduleNumber}";
            $orders[$part] = $this->getMaxQuestionOrder($test, $part);
        }

        return $orders;
    }

    private function getMaxQuestionOrder(Test $test, string $part): int
    {
        if ($test->relationLoaded('questions')) {
            return (int) $test->questions
                ->where('part', $part)
                ->max('question_order');
        }

        return (int) $test->questions()
            ->where('part', $part)
            ->max('question_order');
    }

    private function testHasStudentAttempts(Test $test): bool
    {
        if ($test->relationLoaded('studentTests')) {
            return $test->studentTests->isNotEmpty();
        }

        return $test->studentTests()->exists();
    }

    private function requiredString(mixed $value, string $field): string
    {
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Missing required import field: {$field}.");
        }

        return $value;
    }

    private function requiredInt(mixed $value, string $field): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }

        throw new InvalidArgumentException("Missing required import field: {$field}.");
    }
}
