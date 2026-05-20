<?php

namespace App\Services\Assignments;

class LatexAssignmentParser
{
    private const VALID_TYPES = ['mcq', 'numeric', 'tf', 'essay'];
    private const VALID_DIFFICULTIES = ['easy', 'medium', 'hard'];

    /**
     * Parse a controlled, text-only LaTeX assignment format into a normalized array.
     */
    public function parse(string $latex): array
    {
        $latex = $this->normalizeLineEndings($latex);

        $result = [
            'title' => $this->extractCommandValue($latex, 'assignmenttitle'),
            'questions' => [],
            'errors' => [],
            'warnings' => [],
        ];

        $this->collectUnsupportedContentErrors($latex, $result['errors']);

        $questionBlocks = $this->matchQuestionBlocks($latex);
        $questionSourceIndex = 1;

        foreach ($questionBlocks as $questionBlock) {
            $result['questions'][] = $this->parseQuestionBlock(
                $questionBlock,
                $questionSourceIndex,
                $result['errors'],
                $result['warnings']
            );

            $questionSourceIndex++;
        }

        return $result;
    }

    private function parseQuestionBlock(
        string $block,
        int $sourceIndex,
        array &$errors,
        array &$warnings
    ): array {
        $type = $this->normalizeToken($this->extractCommandValue($block, 'type'));
        $difficulty = $this->normalizeToken($this->extractCommandValue($block, 'difficulty'));
        $points = $this->parsePoints($this->extractCommandValue($block, 'points'));
        $text = $this->extractCommandValue($block, 'text');
        $answer = $this->extractCommandValue($block, 'answer');
        $explanation = $this->extractCommandValue($block, 'explanation');
        $choices = $this->extractChoices($block);

        $prefix = "Question {$sourceIndex}";
        $questionImageSource = $this->extractFirstImageSource(
            $block,
            ['questionimage', 'image'],
            $prefix,
            'question image',
            $warnings
        );
        $explanationImageSource = $this->extractFirstImageSource(
            $block,
            ['explanationimage'],
            $prefix,
            'explanation image',
            $warnings
        );

        if ($type === null || $type === '') {
            $errors[] = "{$prefix}: missing type.";
        } elseif (!in_array($type, self::VALID_TYPES, true)) {
            $errors[] = "{$prefix}: invalid type '{$type}'. Accepted values are mcq, numeric, tf, essay.";
        }

        if ($difficulty === null || $difficulty === '') {
            $difficulty = 'medium';
            $warnings[] = "{$prefix}: missing difficulty; defaulting to medium.";
        } elseif (!in_array($difficulty, self::VALID_DIFFICULTIES, true)) {
            $errors[] = "{$prefix}: invalid difficulty '{$difficulty}'. Accepted values are easy, medium, hard.";
        }

        if ($text === null || trim($text) === '') {
            $errors[] = "{$prefix}: missing text.";
        }

        if ($type === 'mcq') {
            if (count($choices) < 2) {
                $errors[] = "{$prefix}: MCQ questions must have at least 2 choices.";
            }

            if (count($choices) > 6) {
                $errors[] = "{$prefix}: MCQ questions must not have more than 6 choices.";
            }

            if (!$this->hasCorrectChoice($choices)) {
                $errors[] = "{$prefix}: MCQ questions must have at least one correct choice.";
            }

            $answer = null;
        } elseif (in_array($type, ['numeric', 'tf'], true) && ($answer === null || trim($answer) === '')) {
            $errors[] = "{$prefix}: {$type} questions must have an answer.";
        }

        return [
            'source_index' => $sourceIndex,
            'type' => $type,
            'difficulty' => $difficulty,
            'points' => $points,
            'text' => $text,
            'answer' => $type === 'mcq' ? null : $answer,
            'explanation' => $explanation,
            'question_image_source' => $questionImageSource,
            'explanation_image_source' => $explanationImageSource,
            'choices' => $choices,
        ];
    }

    private function matchQuestionBlocks(string $latex): array
    {
        preg_match_all(
            '/\\\\begin\{question\}(.*?)\\\\end\{question\}/s',
            $latex,
            $matches
        );

        return $matches[1] ?? [];
    }

    private function extractChoices(string $block): array
    {
        $choices = [];
        $offset = 0;

        while (preg_match('/\\\\choice\s*\[([^\]]+)\]\s*/s', $block, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $label = $match[1][0];
            $cursor = $match[0][1] + strlen($match[0][0]);

            while (isset($block[$cursor]) && ctype_space($block[$cursor])) {
                $cursor++;
            }

            if (!isset($block[$cursor]) || $block[$cursor] !== '{') {
                $offset = $cursor;
                continue;
            }

            $choiceText = $this->readBalancedBraces($block, $cursor);

            if ($choiceText === null) {
                $offset = $cursor + 1;
                continue;
            }

            $choiceEnd = $this->findBalancedExpressionEnd($block, $cursor);
            $afterChoice = $choiceEnd === null ? '' : substr($block, $choiceEnd);
            $isCorrect = preg_match('/^\s*\\\\correct\b/s', $afterChoice) === 1;

            $choices[] = [
                'label' => trim($label),
                'text' => $this->cleanValue($choiceText),
                'is_correct' => $isCorrect,
            ];

            $offset = $choiceEnd ?? ($cursor + 1);
        }

        return $choices;
    }

    private function extractCommandValue(string $text, string $command): ?string
    {
        $offset = 0;
        $needle = '\\' . $command;

        while (($position = strpos($text, $needle, $offset)) !== false) {
            $cursor = $position + strlen($needle);

            while (isset($text[$cursor]) && ctype_space($text[$cursor])) {
                $cursor++;
            }

            if (!isset($text[$cursor]) || $text[$cursor] !== '{') {
                $offset = $cursor;
                continue;
            }

            $value = $this->readBalancedBraces($text, $cursor);

            return $value === null ? null : $this->cleanValue($value);
        }

        return null;
    }

    private function extractFirstImageSource(
        string $block,
        array $commands,
        string $questionPrefix,
        string $label,
        array &$warnings
    ): ?string {
        $matches = [];

        foreach ($commands as $command) {
            foreach ($this->extractCommandValuesWithPositions($block, $command) as $match) {
                $matches[] = $match;
            }
        }

        if (empty($matches)) {
            return null;
        }

        usort($matches, static fn (array $a, array $b): int => $a['position'] <=> $b['position']);

        if (count($matches) > 1) {
            $warnings[] = "{$questionPrefix}: multiple {$label} references found; keeping the first.";
        }

        return $matches[0]['value'];
    }

    private function extractCommandValuesWithPositions(string $text, string $command): array
    {
        $matches = [];
        $offset = 0;
        $needle = '\\' . $command;

        while (($position = strpos($text, $needle, $offset)) !== false) {
            $cursor = $position + strlen($needle);

            while (isset($text[$cursor]) && ctype_space($text[$cursor])) {
                $cursor++;
            }

            if (!isset($text[$cursor]) || $text[$cursor] !== '{') {
                $offset = $cursor;
                continue;
            }

            $value = $this->readBalancedBraces($text, $cursor);

            if ($value !== null) {
                $matches[] = [
                    'position' => $position,
                    'value' => $this->cleanValue($value),
                ];
            }

            $expressionEnd = $this->findBalancedExpressionEnd($text, $cursor);
            $offset = $expressionEnd ?? ($cursor + 1);
        }

        return $matches;
    }

    private function readBalancedBraces(string $text, int $openBracePosition): ?string
    {
        $depth = 0;
        $start = $openBracePosition + 1;
        $length = strlen($text);

        for ($i = $openBracePosition; $i < $length; $i++) {
            $char = $text[$i];
            $previous = $i > 0 ? $text[$i - 1] : '';

            if ($char === '{' && $previous !== '\\') {
                $depth++;
                continue;
            }

            if ($char === '}' && $previous !== '\\') {
                $depth--;

                if ($depth === 0) {
                    return substr($text, $start, $i - $start);
                }
            }
        }

        return null;
    }

    private function findBalancedExpressionEnd(string $text, int $openBracePosition): ?int
    {
        $depth = 0;
        $length = strlen($text);

        for ($i = $openBracePosition; $i < $length; $i++) {
            $char = $text[$i];
            $previous = $i > 0 ? $text[$i - 1] : '';

            if ($char === '{' && $previous !== '\\') {
                $depth++;
                continue;
            }

            if ($char === '}' && $previous !== '\\') {
                $depth--;

                if ($depth === 0) {
                    return $i + 1;
                }
            }
        }

        return null;
    }

    private function collectUnsupportedContentErrors(string $latex, array &$errors): void
    {
        if (preg_match('/\\\\includegraphics(?:\s*\[[^\]]*\])?\s*\{/s', $latex)) {
            $errors[] = 'Unsupported image content found: \\includegraphics is not supported in the text-only MVP.';
        }

        if (preg_match('/\\\\begin\{tikzpicture\}/s', $latex)) {
            $errors[] = 'Unsupported TikZ content found: tikzpicture is not supported in the text-only MVP.';
        }
    }

    private function hasCorrectChoice(array $choices): bool
    {
        foreach ($choices as $choice) {
            if ($choice['is_correct']) {
                return true;
            }
        }

        return false;
    }

    private function parsePoints(?string $points): ?int
    {
        if ($points === null || trim($points) === '') {
            return null;
        }

        return ctype_digit(trim($points)) ? (int) trim($points) : null;
    }

    private function normalizeLineEndings(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }

    private function normalizeToken(?string $value): ?string
    {
        return $value === null ? null : strtolower(trim($value));
    }

    private function cleanValue(string $value): string
    {
        return trim($this->normalizeLineEndings($value));
    }
}
