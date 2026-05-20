<?php

namespace App\Services\Assignments;

use App\Models\LectureAssignment;
use App\Models\LectureQuestion;
use App\Models\LectureQuestionOption;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class LatexAssignmentImporter
{
    /**
     * Import already validated LaTeX assignment data into an existing assignment.
     *
     * The payload should include parsed questions. If validator output is also
     * present, per-question computed points are read by source_index.
     */
    public function import(LectureAssignment $assignment, array $payload, ?array $archiveImages = null): array
    {
        $copiedPublicPaths = [];

        try {
            return DB::transaction(function () use ($assignment, $payload, $archiveImages, &$copiedPublicPaths): array {
                if ($this->assignmentHasStudentSubmissions($assignment)) {
                    throw new RuntimeException('Selected assignment already has student submissions and cannot be imported into.');
                }

                $pointsMap = $this->buildPointsMap($payload);
                $questionOrder = $this->getMaxQuestionOrder($assignment);
                $importedQuestions = 0;
                $importedOptions = 0;
                $copiedImages = 0;

                foreach ($this->extractParsedQuestions($payload) as $questionData) {
                    $sourceIndex = $this->requiredInt($questionData['source_index'] ?? null, 'source_index');
                    $type = $this->requiredString($questionData['type'] ?? null, "Question {$sourceIndex} type");
                    $difficulty = $this->requiredString($questionData['difficulty'] ?? null, "Question {$sourceIndex} difficulty");
                    $questionText = $this->requiredString($questionData['text'] ?? null, "Question {$sourceIndex} text");
                    $points = $this->resolvePoints($questionData, $pointsMap, $sourceIndex);
                    $questionImagePath = $this->copyReferencedImage(
                        $questionData['question_image_source'] ?? null,
                        $archiveImages,
                        'images/questions',
                        $copiedPublicPaths,
                        "Question {$sourceIndex} question_image_source"
                    );
                    $explanationImagePath = $this->copyReferencedImage(
                        $questionData['explanation_image_source'] ?? null,
                        $archiveImages,
                        'images/explanations',
                        $copiedPublicPaths,
                        "Question {$sourceIndex} explanation_image_source"
                    );

                    if ($questionImagePath !== null) {
                        $copiedImages++;
                    }

                    if ($explanationImagePath !== null) {
                        $copiedImages++;
                    }

                    $questionOrder++;

                    $question = LectureQuestion::create([
                        'lecture_assignment_id' => $assignment->id,
                        'question_text' => $questionText,
                        'type' => $type,
                        'points' => $points,
                        'difficulty' => $difficulty,
                        'correct_answer' => $this->correctAnswerForQuestion($questionData, $type, $sourceIndex),
                        'explanation' => $questionData['explanation'] ?? null,
                        'question_image' => $questionImagePath,
                        'explanation_image' => $explanationImagePath,
                        'order' => $questionOrder,
                    ]);

                    $importedQuestions++;

                    if ($type === 'mcq') {
                        foreach (($questionData['choices'] ?? []) as $index => $choice) {
                            LectureQuestionOption::create([
                                'lecture_question_id' => $question->id,
                                'option_text' => $this->requiredString(
                                    $choice['text'] ?? null,
                                    "Question {$sourceIndex} choice " . ($index + 1)
                                ),
                                'option_image' => null,
                                'is_correct' => !empty($choice['is_correct']),
                                'order' => $index + 1,
                            ]);

                            $importedOptions++;
                        }
                    }
                }

                return [
                    'imported_questions' => $importedQuestions,
                    'imported_options' => $importedOptions,
                    'assignment_id' => $assignment->id,
                    'copied_images' => $copiedImages,
                ];
            });
        } catch (\Throwable $e) {
            $this->deleteCopiedPublicFiles($copiedPublicPaths);

            throw $e;
        }
    }

    private function extractParsedQuestions(array $payload): array
    {
        if (isset($payload['parsed']['questions']) && is_array($payload['parsed']['questions'])) {
            return $payload['parsed']['questions'];
        }

        if (isset($payload['parsed_questions']) && is_array($payload['parsed_questions'])) {
            return $payload['parsed_questions'];
        }

        if (isset($payload['questions']) && is_array($payload['questions'])) {
            return $payload['questions'];
        }

        throw new InvalidArgumentException('Missing parsed assignment questions for import.');
    }

    private function buildPointsMap(array $payload): array
    {
        $questions = [];

        if (isset($payload['validation']['questions']) && is_array($payload['validation']['questions'])) {
            $questions = $payload['validation']['questions'];
        } elseif (isset($payload['validated_questions']) && is_array($payload['validated_questions'])) {
            $questions = $payload['validated_questions'];
        } elseif (isset($payload['questions']) && is_array($payload['questions'])) {
            $questions = $payload['questions'];
        }

        $map = [];

        foreach ($questions as $question) {
            if (isset($question['source_index'], $question['points'])) {
                $map[(int) $question['source_index']] = $question['points'];
            }
        }

        return $map;
    }

    private function resolvePoints(array $questionData, array $pointsMap, int $sourceIndex): int
    {
        $points = $pointsMap[$sourceIndex] ?? ($questionData['points'] ?? null);

        if (!is_numeric($points)) {
            throw new InvalidArgumentException("Question {$sourceIndex} is missing validated points.");
        }

        $points = (int) $points;

        if ($points < 0 || $points > 100) {
            throw new InvalidArgumentException("Question {$sourceIndex} has invalid points {$points}.");
        }

        return $points;
    }

    private function correctAnswerForQuestion(array $questionData, string $type, int $sourceIndex): string
    {
        if ($type === 'mcq' || $type === 'essay') {
            return '';
        }

        return $this->requiredString($questionData['answer'] ?? null, "Question {$sourceIndex} answer");
    }

    private function assignmentHasStudentSubmissions(LectureAssignment $assignment): bool
    {
        if ($assignment->relationLoaded('studentAssignments')) {
            return $assignment->studentAssignments->isNotEmpty();
        }

        return $assignment->studentAssignments()->exists();
    }

    private function getMaxQuestionOrder(LectureAssignment $assignment): int
    {
        if ($assignment->relationLoaded('questions')) {
            return (int) $assignment->questions->max('order');
        }

        return (int) $assignment->questions()->max('order');
    }

    private function copyReferencedImage(
        mixed $imageSource,
        ?array $archiveImages,
        string $publicDirectory,
        array &$copiedPublicPaths,
        string $field
    ): ?string {
        if ($imageSource === null || $imageSource === '') {
            return null;
        }

        if ($archiveImages === null) {
            return null;
        }

        if (!is_string($imageSource)) {
            throw new InvalidArgumentException("Invalid image source for {$field}.");
        }

        $imageSource = trim($imageSource);
        $image = $archiveImages[$imageSource] ?? null;

        if (!is_array($image)) {
            throw new InvalidArgumentException("Referenced image was not found for {$field}: {$imageSource}.");
        }

        $sourcePath = $image['extracted_path'] ?? null;

        if (!is_string($sourcePath) || !is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new RuntimeException("Referenced image file is missing or unreadable for {$field}: {$imageSource}.");
        }

        $extension = strtolower((string) ($image['extension'] ?? pathinfo($sourcePath, PATHINFO_EXTENSION)));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            throw new InvalidArgumentException("Unsupported image extension for {$field}: {$extension}.");
        }

        $this->ensurePublicDirectory($publicDirectory);

        $publicPath = $publicDirectory . '/' . $this->generateImageFilename($extension);
        $targetPath = public_path($publicPath);

        if (!copy($sourcePath, $targetPath)) {
            throw new RuntimeException("Failed to copy image for {$field}: {$imageSource}.");
        }

        $copiedPublicPaths[] = $publicPath;

        return $publicPath;
    }

    private function ensurePublicDirectory(string $publicDirectory): void
    {
        $directory = public_path($publicDirectory);

        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException("Unable to create public image directory: {$publicDirectory}.");
        }
    }

    private function generateImageFilename(string $extension): string
    {
        return date('YmdHis') . '-' . bin2hex(random_bytes(12)) . '.' . $extension;
    }

    private function deleteCopiedPublicFiles(array $publicPaths): void
    {
        foreach (array_reverse($publicPaths) as $publicPath) {
            if (!is_string($publicPath) || $publicPath === '') {
                continue;
            }

            $fullPath = public_path($publicPath);

            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
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
