<?php

namespace App\Services\Tests;

class LatexTestParser
{
    private const VALID_TYPES = ['mcq', 'tf', 'numeric'];
    private const VALID_DIFFICULTIES = ['easy', 'medium', 'hard'];
    private const VALID_CONTENT = [
        'algebra',
        'advanced_math',
        'problem_solving_and_data_analysis',
        'geometry_and_trigonometry',
    ];

    /**
     * Parse a controlled, text-only LaTeX test format into a normalized array.
     */
    public function parse(string $latex): array
    {
        $latex = $this->normalizeLineEndings($latex);

        $result = [
            'title' => $this->extractCommandValue($latex, 'testtitle'),
            'modules' => [],
            'errors' => [],
            'warnings' => [],
        ];

        $this->collectUnsupportedContentErrors($latex, $result['errors']);
        $this->collectQuestionOutsideModuleErrors($latex, $result['errors']);

        $questionSourceIndex = 1;
        $moduleMatches = $this->matchModuleBlocks($latex);

        foreach ($moduleMatches as $moduleMatch) {
            $rawModuleNumber = trim($moduleMatch['number']);
            $moduleNumber = ctype_digit($rawModuleNumber) ? (int) $rawModuleNumber : null;

            if ($moduleNumber === null || $moduleNumber < 1 || $moduleNumber > 5) {
                $result['errors'][] = "Invalid module number '{$rawModuleNumber}'. Module number must be between 1 and 5.";
                continue;
            }

            $module = [
                'module_number' => $moduleNumber,
                'part' => "part{$moduleNumber}",
                'questions' => [],
            ];

            $questionBlocks = $this->matchQuestionBlocks($moduleMatch['body']);

            foreach ($questionBlocks as $questionBlock) {
                $question = $this->parseQuestionBlock(
                    $questionBlock,
                    $questionSourceIndex,
                    $moduleNumber,
                    "part{$moduleNumber}",
                    $result['errors']
                );

                $module['questions'][] = $question;
                $questionSourceIndex++;
            }

            $result['modules'][] = $module;
        }

        return $result;
    }

    private function parseQuestionBlock(
        string $block,
        int $sourceIndex,
        int $moduleNumber,
        string $part,
        array &$errors
    ): array {
        $type = $this->normalizeToken($this->extractCommandValue($block, 'type'));
        $difficulty = $this->normalizeToken($this->extractCommandValue($block, 'difficulty'));
        $content = $this->normalizeToken($this->extractCommandValue($block, 'content'));
        $text = $this->extractCommandValue($block, 'text');
        $answer = $this->extractCommandValue($block, 'answer');
        $explanation = $this->extractCommandValue($block, 'explanation');
        $choices = $this->extractChoices($block);

        $prefix = "Question {$sourceIndex}";

        if ($type === null || $type === '') {
            $errors[] = "{$prefix}: missing type.";
        } elseif (!in_array($type, self::VALID_TYPES, true)) {
            $errors[] = "{$prefix}: invalid type '{$type}'. Accepted values are mcq, tf, numeric.";
        }

        if ($difficulty === null || $difficulty === '') {
            $errors[] = "{$prefix}: missing difficulty.";
        } elseif (!in_array($difficulty, self::VALID_DIFFICULTIES, true)) {
            $errors[] = "{$prefix}: invalid difficulty '{$difficulty}'. Accepted values are easy, medium, hard.";
        }

        if ($content === null || $content === '') {
            $errors[] = "{$prefix}: missing content.";
        } elseif (!in_array($content, self::VALID_CONTENT, true)) {
            $errors[] = "{$prefix}: invalid content '{$content}'.";
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
            'module_number' => $moduleNumber,
            'part' => $part,
            'type' => $type,
            'difficulty' => $difficulty,
            'content' => $content,
            'text' => $text,
            'choices' => $choices,
            'answer' => $type === 'mcq' ? null : $answer,
            'explanation' => $explanation,
        ];
    }

    private function matchModuleBlocks(string $latex): array
    {
        preg_match_all(
            '/\\\\begin\{module\}\s*\{([^}]*)\}(.*?)\\\\end\{module\}/s',
            $latex,
            $matches,
            PREG_SET_ORDER
        );

        return array_map(static function (array $match): array {
            return [
                'number' => $match[1],
                'body' => $match[2],
            ];
        }, $matches);
    }

    private function matchQuestionBlocks(string $moduleBody): array
    {
        preg_match_all(
            '/\\\\begin\{question\}(.*?)\\\\end\{question\}/s',
            $moduleBody,
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

    private function collectQuestionOutsideModuleErrors(string $latex, array &$errors): void
    {
        $withoutModules = preg_replace(
            '/\\\\begin\{module\}\s*\{[^}]*\}.*?\\\\end\{module\}/s',
            '',
            $latex
        );

        if ($withoutModules !== null && preg_match('/\\\\begin\{question\}/', $withoutModules)) {
            $errors[] = 'Question found outside a module block. Every question must be inside \\begin{module}{N} ... \\end{module}.';
        }
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
