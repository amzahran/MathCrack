<?php

namespace App\Services\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiExplanationService
{
    public const UNAVAILABLE_MESSAGE = 'AI explanation is currently unavailable.';
    public const FAILURE_MESSAGE = 'AI explanation could not be generated. Please try again later.';

    public function generate(Model $question, string $source): array
    {
        if (!$this->isAvailable()) {
            return [
                'success' => false,
                'message' => self::UNAVAILABLE_MESSAGE,
                'unavailable' => true,
            ];
        }

        if (config('services.ai_explanation.provider') !== 'openai') {
            return [
                'success' => false,
                'message' => self::UNAVAILABLE_MESSAGE,
                'unavailable' => true,
            ];
        }

        try {
            $response = Http::withToken(config('services.ai_explanation.api_key'))
                ->timeout((int) config('services.ai_explanation.timeout', 30))
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model(),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a concise SAT math tutor. Return short plain-text explanations with this exact structure: Step 1: ..., Step 2: ..., Step 3: ..., Final answer: .... Format all mathematical expressions as LaTeX for MathJax: use \\( ... \\) for inline math and \\[ ... \\] for important equations. Do not use Markdown symbols like **, ###, or bullet-heavy formatting. Do not mention AI.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($question, $source),
                        ],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 500,
                ]);

            if (!$response->successful()) {
                Log::warning('AI explanation request failed', [
                    'question_type' => $source,
                    'question_id' => $question->id,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'message' => self::FAILURE_MESSAGE,
                ];
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content'));

            if ($content === '') {
                Log::warning('AI explanation empty response', [
                    'question_type' => $source,
                    'question_id' => $question->id,
                ]);

                return [
                    'success' => false,
                    'message' => self::FAILURE_MESSAGE,
                ];
            }

            return [
                'success' => true,
                'explanation' => $content,
            ];
        } catch (\Throwable $e) {
            Log::error('AI explanation request exception', [
                'question_type' => $source,
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => self::FAILURE_MESSAGE,
            ];
        }
    }

    private function isAvailable(): bool
    {
        return (bool) config('services.ai_explanation.enabled')
            && filled(config('services.ai_explanation.api_key'));
    }

    private function model(): string
    {
        return filled(config('services.ai_explanation.model'))
            ? config('services.ai_explanation.model')
            : 'gpt-4o-mini';
    }

    private function buildPrompt(Model $question, string $source): string
    {
        $choices = $this->formatChoices($question);
        $correctAnswer = $this->resolveCorrectAnswer($question);

        return implode("\n\n", array_filter([
            'Question source/type: ' . $source,
            'Question text: ' . trim(strip_tags((string) $question->question_text)),
            $choices ? "Answer choices:\n" . $choices : null,
            $correctAnswer ? 'Correct answer: ' . $correctAnswer : null,
            "Write a concise SAT-style explanation using exactly this structure: Step 1: ..., Step 2: ..., Step 3: ..., Final answer: .... Format every mathematical expression with LaTeX for MathJax. Use \\( ... \\) for inline math and use displayed equations like:\n\\[\n...\n\\]\nfor important equations. Keep it short. Do not use Markdown symbols such as **, ###, or bullet-heavy formatting. Do not mention that the explanation is AI-generated.",
        ]));
    }

    private function formatChoices(Model $question): string
    {
        if (!$question->relationLoaded('options')) {
            $question->load('options');
        }

        if (!$question->options || $question->options->isEmpty()) {
            return '';
        }

        return $question->options->values()->map(function ($option, $index) {
            $label = $option->label ?? chr(65 + $index);
            $text = $option->option_text ?? '';

            return $label . '. ' . trim(strip_tags((string) $text));
        })->implode("\n");
    }

    private function resolveCorrectAnswer(Model $question): string
    {
        if (filled($question->correct_answer)) {
            return trim((string) $question->correct_answer);
        }

        if (!$question->relationLoaded('options')) {
            $question->load('options');
        }

        if (!$question->options) {
            return '';
        }

        return $question->options
            ->where('is_correct', true)
            ->map(function ($option) {
                return trim(strip_tags((string) ($option->option_text ?? '')));
            })
            ->filter()
            ->implode(', ');
    }
}
