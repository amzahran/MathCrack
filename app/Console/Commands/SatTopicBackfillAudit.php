<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SatTopicBackfillAudit extends Command
{
    protected $signature = 'sat:topic-backfill-audit
                            {--format=csv : Export format; currently only csv is supported}
                            {--output=storage/app/sat-topic-audit.csv : CSV output path}
                            {--test= : Restrict the audit to one SAT test ID}
                            {--limit= : Limit the number of untagged questions exported}';

    protected $description = 'Export untagged SAT question topic suggestions for manual review (read-only database audit)';

    private const DOMAINS = [
        'algebra' => 'Algebra',
        'advanced_math' => 'Advanced Math',
        'problem_solving_and_data_analysis' => 'Problem Solving and Data Analysis',
        'geometry_and_trigonometry' => 'Geometry and Trigonometry',
    ];

    private const HEADERS = [
        'test_id',
        'test_title',
        'question_id',
        'question_order',
        'question_text_preview',
        'full_question_text',
        'options_preview',
        'current_content',
        'suggested_domain_label',
        'suggested_domain_slug',
        'confidence',
        'image_present',
        'explanation_present',
        'needs_manual_review',
    ];

    public function handle(): int
    {
        if (strtolower((string) $this->option('format')) !== 'csv') {
            $this->error('Only --format=csv is supported.');

            return self::FAILURE;
        }

        $testId = $this->validatedTestId();
        if ($testId === false) {
            return self::FAILURE;
        }

        $limit = $this->validatedLimit();
        if ($limit === false) {
            return self::FAILURE;
        }

        $satTestsQuery = $this->satTestsQuery($testId);
        $testsFound = (clone $satTestsQuery)->count();
        $testIds = (clone $satTestsQuery)->pluck('tests.id');

        if ($testsFound === 0) {
            $this->warn('No SAT or Digital SAT tests matched the requested filter.');

            return self::SUCCESS;
        }

        $summary = $this->questionSummary($testIds->all());
        $questions = $this->untaggedQuestions($testIds->all(), $limit);
        $optionsByQuestion = $this->optionsForQuestions($questions->pluck('question_id')->all());

        $rows = [];
        $confidenceCounts = ['high' => 0, 'medium' => 0, 'low' => 0];
        $imagePresentCount = 0;

        foreach ($questions as $question) {
            $options = $optionsByQuestion->get($question->question_id, collect());
            $imagePresent = $this->hasImage($question, $options);
            $suggestion = $this->classify($question->question_text, $options);
            $needsManualReview = $suggestion['confidence'] === 'low' || $imagePresent;

            $confidenceCounts[$suggestion['confidence']]++;
            if ($imagePresent) {
                $imagePresentCount++;
            }

            $rows[] = [
                $question->test_id,
                $question->test_title,
                $question->question_id,
                $question->question_order,
                $this->preview($question->question_text, 180),
                $this->cleanText($question->question_text),
                $this->optionsPreview($options),
                $question->current_content,
                $suggestion['label'],
                $suggestion['slug'],
                $suggestion['confidence'],
                $imagePresent ? 'yes' : 'no',
                $this->hasExplanation($question) ? 'yes' : 'no',
                $needsManualReview ? 'yes' : 'no',
            ];
        }

        $outputPath = $this->resolveOutputPath((string) $this->option('output'));
        if (!$this->writeCsv($outputPath, $rows)) {
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('SAT topic backfill audit export complete. No database records were modified.');
        $this->line('Total SAT tests found: ' . $testsFound);
        $this->line('Total questions scanned: ' . $summary->total_questions);
        $this->line('Total untagged: ' . $summary->untagged_questions);
        $this->line('Rows exported: ' . count($rows));
        $this->line('High confidence count: ' . $confidenceCounts['high']);
        $this->line('Medium confidence count: ' . $confidenceCounts['medium']);
        $this->line('Low confidence count: ' . $confidenceCounts['low']);
        $this->line('Image-present count: ' . $imagePresentCount);
        $this->line('Output file path: ' . $outputPath);

        return self::SUCCESS;
    }

    private function validatedTestId()
    {
        $testId = $this->option('test');

        if ($testId === null || $testId === '') {
            return null;
        }

        if (!ctype_digit((string) $testId) || (int) $testId < 1) {
            $this->error('--test must be a positive integer ID.');

            return false;
        }

        return (int) $testId;
    }

    private function validatedLimit()
    {
        $limit = $this->option('limit');

        if ($limit === null || $limit === '') {
            return null;
        }

        if (!ctype_digit((string) $limit) || (int) $limit < 1) {
            $this->error('--limit must be a positive integer.');

            return false;
        }

        return (int) $limit;
    }

    private function satTestsQuery(?int $testId): Builder
    {
        return DB::table('tests')
            ->where(function (Builder $query): void {
                $query->whereRaw('LOWER(tests.name) LIKE ?', ['%digital sat%'])
                    ->orWhereRaw('LOWER(tests.name) = ?', ['sat'])
                    ->orWhereRaw('LOWER(tests.name) LIKE ?', ['sat %'])
                    ->orWhereRaw('LOWER(tests.name) LIKE ?', ['% sat %'])
                    ->orWhereRaw('LOWER(tests.name) LIKE ?', ['% sat']);
            })
            ->when($testId !== null, function (Builder $query) use ($testId): void {
                $query->where('tests.id', $testId);
            });
    }

    private function questionSummary(array $testIds): object
    {
        return DB::table('test_questions')
            ->whereIn('test_id', $testIds)
            ->selectRaw('COUNT(*) as total_questions')
            ->selectRaw("SUM(CASE WHEN content IS NULL OR TRIM(content) = '' THEN 1 ELSE 0 END) as untagged_questions")
            ->first();
    }

    private function untaggedQuestions(array $testIds, ?int $limit)
    {
        $query = DB::table('test_questions as q')
            ->join('tests as t', 't.id', '=', 'q.test_id')
            ->whereIn('q.test_id', $testIds)
            ->where(function (Builder $query): void {
                $query->whereNull('q.content')
                    ->orWhereRaw("TRIM(q.content) = ''");
            })
            ->select([
                'q.test_id',
                't.name as test_title',
                'q.id as question_id',
                'q.question_order',
                'q.question_text',
                'q.question_image',
                'q.explanation',
                'q.explanation_image',
                'q.content as current_content',
            ])
            ->orderBy('q.test_id')
            ->orderBy('q.module_number')
            ->orderBy('q.question_order')
            ->orderBy('q.id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    private function optionsForQuestions(array $questionIds)
    {
        if (empty($questionIds)) {
            return collect();
        }

        return DB::table('test_question_options')
            ->whereIn('test_question_id', $questionIds)
            ->select(['test_question_id', 'option_text', 'option_image', 'option_order'])
            ->orderBy('test_question_id')
            ->orderBy('option_order')
            ->get()
            ->groupBy('test_question_id');
    }

    private function classify(?string $questionText, $options): array
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

    private function hasImage(object $question, $options): bool
    {
        if ($this->hasValue($question->question_image)) {
            return true;
        }

        return $options->contains(function (object $option): bool {
            return $this->hasValue($option->option_image);
        });
    }

    private function hasExplanation(object $question): bool
    {
        return $this->hasValue($question->explanation) || $this->hasValue($question->explanation_image);
    }

    private function optionsPreview($options): string
    {
        return $options->map(function (object $option): string {
            $label = chr(65 + max(0, (int) $option->option_order - 1));

            return $label . ': ' . $this->cleanText($option->option_text);
        })->implode(' | ');
    }

    private function preview(?string $text, int $length): string
    {
        $text = $this->cleanText($text);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3) . '...';
    }

    private function cleanText(?string $text): string
    {
        $text = trim(strip_tags((string) $text));

        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    private function hasValue(?string $value): bool
    {
        return trim((string) $value) !== '';
    }

    private function resolveOutputPath(string $output): string
    {
        if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/]{2}|\/)/', $output) === 1) {
            return $output;
        }

        return base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $output));
    }

    private function writeCsv(string $path, array $rows): bool
    {
        $directory = dirname($path);

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            $this->error('Unable to open CSV output path: ' . $path);

            return false;
        }

        fputcsv($handle, self::HEADERS);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return true;
    }
}
