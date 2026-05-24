<?php

namespace App\Console\Commands;

use App\Services\Tests\SatTopicClassifier;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SatTopicBackfillSmart extends Command
{
    protected $signature = 'sat:topic-backfill-smart
                            {--dry-run : Preview eligible updates without modifying database records}
                            {--apply : Apply only high-confidence, image-free suggestions to untagged math questions}
                            {--limit= : Limit evaluated untagged questions for dry-run testing only}
                            {--output=storage/app/sat-topic-backfill-smart-preview.csv : Preview CSV output path}
                            {--test= : Restrict the operation to one test ID}';

    protected $description = 'Preview or safely backfill high-confidence math topic values across tests';

    private const HEADERS = [
        'test_id',
        'test_title',
        'question_id',
        'question_order',
        'question_text_preview',
        'current_content',
        'suggested_domain_label',
        'suggested_domain_slug',
        'confidence',
        'image_present',
        'eligible_for_update',
        'skip_reason',
    ];

    public function handle(SatTopicClassifier $classifier): int
    {
        $apply = (bool) $this->option('apply');

        if ($apply && (bool) $this->option('dry-run')) {
            $this->error('Choose either --dry-run or --apply, not both.');

            return self::FAILURE;
        }

        $testId = $this->validatedPositiveIntegerOption('test');
        if ($testId === false) {
            return self::FAILURE;
        }

        $limit = $this->validatedPositiveIntegerOption('limit');
        if ($limit === false) {
            return self::FAILURE;
        }

        if ($apply && $limit !== null) {
            $this->error('--limit is for dry-run testing only and cannot be combined with --apply.');

            return self::FAILURE;
        }

        if ($apply) {
            $this->warn('APPLY MODE: updating high-confidence untagged math questions only.');
        } else {
            $this->info('DRY RUN: no database records will be modified.');
        }

        $testsQuery = $this->testsQuery($testId);
        $testsFound = (clone $testsQuery)->count();
        $testIds = (clone $testsQuery)
            ->join('test_questions', 'test_questions.test_id', '=', 'tests.id')
            ->distinct()
            ->pluck('tests.id')
            ->all();
        $testsWithQuestions = count($testIds);

        if ($testsWithQuestions === 0) {
            $this->warn('No tests with questions matched the requested filter.');

            return self::SUCCESS;
        }

        $summary = $this->questionSummary($testIds);
        $questions = $this->untaggedQuestions($testIds, $limit);
        $optionsByQuestion = $this->optionsForQuestions($questions->pluck('question_id')->all());

        $rows = [];
        $eligible = [];
        $counts = [
            'medium' => 0,
            'low' => 0,
            'image' => 0,
            'no_suggestion' => 0,
        ];
        $suggestedDistribution = array_fill_keys(array_keys(SatTopicClassifier::DOMAINS), 0);
        $eligibleDistribution = array_fill_keys(array_keys(SatTopicClassifier::DOMAINS), 0);

        foreach ($questions as $question) {
            $options = $optionsByQuestion->get($question->question_id, collect());
            $suggestion = $classifier->classify($question->question_text, $options);
            $imagePresent = $classifier->hasImage($question, $options);
            $allowedSlug = isset(SatTopicClassifier::DOMAINS[$suggestion['slug']]);
            $isEligible = $suggestion['confidence'] === 'high' && !$imagePresent && $allowedSlug;
            $skipReason = '';

            if ($allowedSlug) {
                $suggestedDistribution[$suggestion['slug']]++;
            }
            if ($imagePresent) {
                $counts['image']++;
            }
            if ($suggestion['confidence'] === 'medium') {
                $counts['medium']++;
                $skipReason = 'medium confidence';
            } elseif ($suggestion['confidence'] === 'low') {
                $counts['low']++;
                $skipReason = 'low confidence';
            }
            if ($suggestion['slug'] === '') {
                $counts['no_suggestion']++;
                $skipReason = 'no confident suggestion';
            }
            if ($imagePresent) {
                $skipReason = 'image present';
            }

            if ($isEligible) {
                $eligible[] = [
                    'question_id' => (int) $question->question_id,
                    'slug' => $suggestion['slug'],
                ];
                $eligibleDistribution[$suggestion['slug']]++;
            }

            $rows[] = [
                $question->test_id,
                $question->test_title,
                $question->question_id,
                $question->question_order,
                $classifier->preview($question->question_text, 180),
                $question->current_content,
                $suggestion['label'],
                $suggestion['slug'],
                $suggestion['confidence'],
                $imagePresent ? 'yes' : 'no',
                $isEligible ? 'yes' : 'no',
                $isEligible ? '' : $skipReason,
            ];
        }

        $outputPath = $this->resolveOutputPath((string) $this->option('output'));
        if (!$this->writeCsv($outputPath, $rows)) {
            return self::FAILURE;
        }

        $updated = 0;
        $updatedDistribution = array_fill_keys(array_keys(SatTopicClassifier::DOMAINS), 0);
        if ($apply) {
            DB::transaction(function () use ($eligible, $testIds, &$updated, &$updatedDistribution): void {
                foreach ($eligible as $candidate) {
                    if (!isset(SatTopicClassifier::DOMAINS[$candidate['slug']])) {
                        continue;
                    }

                    $affected = DB::table('test_questions')
                        ->where('id', $candidate['question_id'])
                        ->whereIn('test_id', $testIds)
                        ->where(function (Builder $query): void {
                            $query->whereNull('content')
                                ->orWhereRaw("TRIM(content) = ''");
                        })
                        ->where(function (Builder $query): void {
                            $query->whereNull('question_image')
                                ->orWhereRaw("TRIM(question_image) = ''");
                        })
                        ->whereNotExists(function (Builder $query): void {
                            $query->select(DB::raw(1))
                                ->from('test_question_options as image_options')
                                ->whereColumn('image_options.test_question_id', 'test_questions.id')
                                ->whereNotNull('image_options.option_image')
                                ->whereRaw("TRIM(image_options.option_image) <> ''");
                        })
                        ->update(['content' => $candidate['slug']]);

                    $updated += $affected;
                    $updatedDistribution[$candidate['slug']] += $affected;
                }
            });
        }

        $this->printSummary(
            $apply,
            $testsFound,
            $testsWithQuestions,
            $summary,
            count($rows),
            count($eligible),
            $counts,
            $suggestedDistribution,
            $apply ? $updatedDistribution : $eligibleDistribution,
            $updated,
            $outputPath,
            $limit
        );
        $this->printSampleRows($rows, $eligible);

        return self::SUCCESS;
    }

    private function validatedPositiveIntegerOption(string $name)
    {
        $value = $this->option($name);

        if ($value === null || $value === '') {
            return null;
        }

        if (!ctype_digit((string) $value) || (int) $value < 1) {
            $this->error("--{$name} must be a positive integer.");

            return false;
        }

        return (int) $value;
    }

    private function testsQuery(?int $testId): Builder
    {
        return DB::table('tests')
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
            ->selectRaw("SUM(CASE WHEN content IS NOT NULL AND TRIM(content) <> '' THEN 1 ELSE 0 END) as tagged_questions")
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

    private function printSummary(
        bool $apply,
        int $testsFound,
        int $testsWithQuestions,
        object $summary,
        int $rowsEvaluated,
        int $eligibleCount,
        array $counts,
        array $suggestedDistribution,
        array $eligibleOrUpdatedDistribution,
        int $updated,
        string $outputPath,
        ?int $limit
    ): void {
        $this->newLine();
        $this->info($apply ? 'Math topic smart backfill apply complete.' : 'Math topic smart backfill dry-run complete.');
        $this->line('Total tests found: ' . $testsFound);
        $this->line('Total tests with questions: ' . $testsWithQuestions);
        $this->line('Total questions scanned: ' . (int) $summary->total_questions);
        $this->line('Already tagged: ' . (int) $summary->tagged_questions);
        $this->line('Untagged: ' . (int) $summary->untagged_questions);
        $this->line('Rows evaluated' . ($limit !== null ? ' (limited preview)' : '') . ': ' . $rowsEvaluated);
        $this->line('Eligible high-confidence updates: ' . $eligibleCount);
        $this->line('Skipped medium confidence: ' . $counts['medium']);
        $this->line('Skipped low confidence: ' . $counts['low']);
        $this->line('Skipped image-present: ' . $counts['image']);
        $this->line('Skipped no suggestion: ' . $counts['no_suggestion']);

        if ($apply) {
            $this->line('Total updated rows: ' . $updated);
        }

        $this->newLine();
        $this->line('Distribution by suggested topic (nonblank evaluated suggestions):');
        foreach (SatTopicClassifier::DOMAINS as $slug => $label) {
            $this->line("  {$label}: " . $suggestedDistribution[$slug]);
        }

        $this->newLine();
        $this->line($apply ? 'Updated distribution by topic:' : 'Eligible distribution by suggested topic:');
        foreach (SatTopicClassifier::DOMAINS as $slug => $label) {
            $this->line("  {$label}: " . $eligibleOrUpdatedDistribution[$slug]);
        }

        $this->line('Preview output file path: ' . $outputPath);
        if ($apply) {
            $this->warn('Medium-confidence, low-confidence, image-present, already-tagged, and questions without a safe math-domain suggestion were not touched.');
        } else {
            $this->info('Dry-run only: rerun with --apply to update eligible rows.');
        }
    }

    private function printSampleRows(array $rows, array $eligible): void
    {
        if (empty($eligible)) {
            return;
        }

        $eligibleIds = array_column($eligible, 'question_id');
        $sample = collect($rows)
            ->filter(function (array $row) use ($eligibleIds): bool {
                return in_array((int) $row[2], $eligibleIds, true);
            })
            ->take(5)
            ->map(function (array $row): array {
                return [
                    'Test' => $row[0],
                    'Question' => $row[2],
                    'Suggested Topic' => $row[6],
                    'Confidence' => $row[8],
                    'Preview' => $row[4],
                ];
            })
            ->all();

        $this->newLine();
        $this->line('Sample rows that would be updated:');
        $this->table(['Test', 'Question', 'Suggested Topic', 'Confidence', 'Preview'], $sample);
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
            $this->error('Unable to open preview CSV output path: ' . $path);

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
