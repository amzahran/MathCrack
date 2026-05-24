<?php

namespace App\Services\Tests;

class SatTopicClassifier
{
    public const DOMAINS = [
        'algebra' => 'Algebra',
        'advanced_math' => 'Advanced Math',
        'problem_solving_and_data_analysis' => 'Problem Solving and Data Analysis',
        'geometry_and_trigonometry' => 'Geometry and Trigonometry',
    ];

    public function classify(?string $questionText, $options): array
    {
        $searchable = strtolower($this->cleanText($questionText) . ' ' . $this->optionsPreview($options));
        $matches = [];

        $rules = [
            'geometry_and_trigonometry' => [
                'confidence' => 'high',
                'pattern' => '/\b(triangle|circle|radius|diameter|circumference|angle|parallel|perpendicular|congruent|similar triangles?|right triangle|sine|cosine|tangent|trigonometric|cylinder|cone|sphere|rectangular prism)\b/i',
            ],
            'problem_solving_and_data_analysis' => [
                'confidence' => 'high',
                'pattern' => '/\b(random sample|probability|scatterplot|scatter plot|line of best fit|margin of error|survey|frequency table|histogram|median|standard deviation|data set|percentile|two-way table)\b/i',
            ],
            'advanced_math' => [
                'confidence' => 'medium',
                'pattern' => '/\b(quadratic|polynomial|parabola|exponential|radical equation|rational function|nonlinear|zeros? of|complex number)\b/i',
            ],
            'algebra' => [
                'confidence' => 'medium',
                'pattern' => '/\b(linear equation|system of (?:linear )?equations?|slope|x-intercept|y-intercept|linear function|inequality|equation of a line)\b/i',
            ],
        ];

        foreach ($rules as $slug => $rule) {
            if (preg_match($rule['pattern'], $searchable) === 1) {
                $matches[$slug] = $rule['confidence'];
            }
        }

        if (count($matches) !== 1) {
            return ['label' => '', 'slug' => '', 'confidence' => 'low'];
        }

        $slug = array_key_first($matches);

        return [
            'label' => self::DOMAINS[$slug],
            'slug' => $slug,
            'confidence' => $matches[$slug],
        ];
    }

    public function hasImage(object $question, $options): bool
    {
        if ($this->hasValue($question->question_image)) {
            return true;
        }

        return $options->contains(function (object $option): bool {
            return $this->hasValue($option->option_image);
        });
    }

    public function hasExplanation(object $question): bool
    {
        return $this->hasValue($question->explanation) || $this->hasValue($question->explanation_image);
    }

    public function optionsPreview($options): string
    {
        return $options->map(function (object $option): string {
            $label = chr(65 + max(0, (int) $option->option_order - 1));

            return $label . ': ' . $this->cleanText($option->option_text);
        })->implode(' | ');
    }

    public function preview(?string $text, int $length): string
    {
        $text = $this->cleanText($text);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3) . '...';
    }

    public function cleanText(?string $text): string
    {
        $text = trim(strip_tags((string) $text));

        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    private function hasValue(?string $value): bool
    {
        return trim((string) $value) !== '';
    }
}
