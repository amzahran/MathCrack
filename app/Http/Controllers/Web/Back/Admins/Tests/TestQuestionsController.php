<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestQuestionsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->test_id));
        if (!$test) {
            return redirect()->route('dashboard.admins.tests')->with('error', __('l.test_not_found'));
        }

        $questions = TestQuestion::where('test_id', $test->id)
            ->with('options')
            ->orderBy('part')
            ->orderBy('question_order')
            ->get();

        $partLabels = [
            'part1' => __('l.first_part'),
            'part2' => __('l.second_part'),
            'part3' => __('l.third_part'),
            'part4' => __('l.fourth_part'),
            'part5' => __('l.fifth_part'),
        ];

        $modules = [];
        $allParts = ['part1', 'part2', 'part3', 'part4', 'part5'];

        foreach ($allParts as $partKey) {
            $countField = $partKey . '_questions_count';
            $maxQuestions = (int) ($test->$countField ?? 0);

            $currentCount = $test->questions()
                ->where('part', $partKey)
                ->count();

            if ($maxQuestions <= 0) {
                $remaining = 0;
                $canAdd   = true;
            } else {
                $remaining = max($maxQuestions - $currentCount, 0);
                $canAdd   = $remaining > 0;
            }

            $modules[$partKey] = [
                'number'    => str_replace('part', '', $partKey),
                'key'       => $partKey,
                'label'     => $partLabels[$partKey] ?? "Module " . str_replace('part', '', $partKey),
                'current'   => $currentCount,
                'max'       => $maxQuestions,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        $allModulesComplete = true;
        foreach ($modules as $module) {
            if ($module['remaining'] > 0) {
                $allModulesComplete = false;
                break;
            }
        }

        $availableParts = [];
        foreach ($allParts as $partKey) {
            $countField = $partKey . '_questions_count';
            $max = (int) ($test->$countField ?? 0);
            $current = $test->questions()->where('part', $partKey)->count();

            if ($max <= 0) {
                $remaining = 0;
                $canAdd    = true;
            } else {
                $remaining = max($max - $current, 0);
                $canAdd    = $remaining > 0;
            }

            $availableParts[$partKey] = [
                'label'     => $partLabels[$partKey] ?? "Module " . str_replace('part', '', $partKey),
                'current'   => $current,
                'max'       => $max,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        $questionStatus = [
            'part1_count'     => $test->questions()->where('part', 'part1')->count(),
            'part1_max'       => $test->part1_questions_count ?? 0,
            'part1_complete'  => $test->questions()->where('part', 'part1')->count() >= ($test->part1_questions_count ?? 0),

            'part2_count'     => $test->questions()->where('part', 'part2')->count(),
            'part2_max'       => $test->part2_questions_count ?? 0,
            'part2_complete'  => $test->questions()->where('part', 'part2')->count() >= ($test->part2_questions_count ?? 0),

            'part3_count'     => $test->questions()->where('part', 'part3')->count(),
            'part3_max'       => $test->part3_questions_count ?? 0,
            'part3_complete'  => $test->questions()->where('part', 'part3')->count() >= ($test->part3_questions_count ?? 0),

            'part4_count'     => $test->questions()->where('part', 'part4')->count(),
            'part4_max'       => $test->part4_questions_count ?? 0,
            'part4_complete'  => $test->questions()->where('part', 'part4')->count() >= ($test->part4_questions_count ?? 0),

            'part5_count'     => $test->questions()->where('part', 'part5')->count(),
            'part5_max'       => $test->part5_questions_count ?? 0,
            'part5_complete'  => $test->questions()->where('part', 'part5')->count() >= ($test->part5_questions_count ?? 0),

            'all_complete'    => $this->areAllQuestionsAdded($test),
        ];

        return view('themes.default.back.admins.tests.questions.index', compact(
            'test',
            'questions',
            'modules',
            'allModulesComplete',
            'availableParts',
            'questionStatus'
        ));
    }

    private function areAllQuestionsAdded(Test $test): bool
    {
        $parts = ['part1', 'part2', 'part3', 'part4', 'part5'];

        foreach ($parts as $part) {
            $countField = $part . '_questions_count';
            $maxCount   = $test->$countField ?? 0;

            if ($maxCount > 0) {
                $currentCount = $test->questions()->where('part', $part)->count();
                if ($currentCount < $maxCount) {
                    return false;
                }
            }
        }

        return true;
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $test = Test::find(decrypt($request->test_id));
        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => __('l.test_not_found')
            ], 404);
        }

        $this->prepareStoreRequestForValidation($request, $test);

        $validator = Validator::make($request->all(), [
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,tf,numeric',
            'part'                   => 'nullable|in:part1,part2,part3,part4,part5',
            'score'                  => 'required|integer|min:1|max:100',
            'difficulty'             => 'required|in:easy,medium,hard',
            'content'                => 'required|in:algebra,advanced_math,problem_solving_and_data_analysis,geometry_and_trigonometry',
            'explanation'            => 'nullable|string',
            'question_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'explanation_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'correct_answer'         => 'required_if:type,tf,numeric|nullable',
            'options'                => 'required_if:type,mcq|array|max:6',
            'options.*.option_text'  => 'nullable|string',
            'options.*.is_correct'   => 'nullable|boolean',
            'options.*.option_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        $submittedOptions = $request->all()['options'] ?? [];

        if ($request->type === 'mcq' && $request->has('options')) {
            $hasCorrectAnswer = false;
            foreach ($submittedOptions as $option) {
                if (isset($option['is_correct']) && $option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.must_select_correct_answer'),
                    'errors'  => ['options' => [__('l.must_select_correct_answer')]]
                ], 422);
            }
        }

        $submittedOptions = $request->all()['options'] ?? [];

        if ($optionCountErrors = $this->validateCompleteMcqOptions($submittedOptions, $request->type)) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $optionCountErrors
            ], 422);
        }

        if ($duplicateErrors = $this->validateUniqueMcqOptions($submittedOptions, $request->type)) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $duplicateErrors
            ], 422);
        }

        $this->keepOnlyUsableMcqOptionsForSaving($request);

        $selectedPart = $request->part;
        $countField   = $selectedPart . '_questions_count';

        $maxQuestions = (int) ($test->$countField ?? 0);
        $currentCount = $test->questions()->where('part', $selectedPart)->count();

        if ($maxQuestions > 0 && $currentCount >= $maxQuestions) {
            return response()->json([
                'success' => false,
                'message' => __('l.part_questions_complete'),
                'errors'  => ['part' => [__('l.part_questions_complete')]]
            ], 400);
        }

        try {
            DB::beginTransaction();

            $questionOrder = TestQuestion::where('test_id', $test->id)
                ->where('part', $selectedPart)
                ->max('question_order') ?? 0;
            $questionOrder = $questionOrder + 1;

            $questionImagePath = null;
            if ($request->hasFile('question_image')) {
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            $explanationImagePath = null;
            if ($request->hasFile('explanation_image')) {
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question = TestQuestion::create([
                'test_id'           => $test->id,
                'question_text'     => $request->question_text,
                'explanation'       => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image'    => $questionImagePath,
                'type'              => $request->type,
                'part'              => $selectedPart,
                'question_order'    => $questionOrder,
                'score'             => $request->score,
                'difficulty'        => $request->difficulty,
                'content'           => $request->content,
                'correct_answer'    => $request->type === 'mcq' ? '' : $request->correct_answer
            ]);

            if ($request->type === 'mcq' && $request->has('options')) {
                Log::info('Creating MCQ options', ['options' => $request->options]);

                foreach ($request->options as $index => $optionData) {
                    $optionImagePath = null;

                    if (isset($optionData['option_image']) && $optionData['option_image']) {
                        $optionImagePath = upload_to_public($optionData['option_image'], 'images/options');
                    }

                    TestQuestionOption::create([
                        'test_question_id' => $question->id,
                        'option_text'      => $optionData['option_text'] ?? '',
                        'option_image'     => $optionImagePath,
                        'is_correct'       => isset($optionData['is_correct']) && $optionData['is_correct'] == true,
                        'option_order'     => $index + 1
                    ]);
                }
            }

            DB::commit();

            $partLabels = [
                'part1' => __('l.first_part'),
                'part2' => __('l.second_part'),
                'part3' => __('l.third_part'),
                'part4' => __('l.fourth_part'),
                'part5' => __('l.fifth_part'),
            ];

            $partName = $partLabels[$selectedPart] ?? $selectedPart;
            $currentCount = TestQuestion::where('test_id', $test->id)
                ->where('part', $selectedPart)
                ->count();

            $remaining = max($maxQuestions - $currentCount, 0);

            return response()->json([
                'success'       => true,
                'message'       => __('l.question_created_successfully'),
                'part_name'     => $partName,
                'current_count' => $currentCount,
                'max_count'     => $maxQuestions,
                'remaining'     => $remaining,
                'question_id'   => $question->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating test question: ' . $e->getMessage(), [
                'test_id'      => $test->id,
                'request_data' => $request->except(['question_image', 'explanation_image']),
                'trace'        => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->test_id));
        $question = TestQuestion::with('options')->find(decrypt($request->id));

        if (!$test || !$question || $question->test_id !== $test->id) {
            return redirect()->back()->with('error', __('l.question_not_found'));
        }

        return view('themes.default.back.admins.tests.questions.edit', compact('test', 'question'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $question = TestQuestion::with('options')->find($request->id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => __('l.question_not_found')
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,tf,numeric',
            'part'                   => 'required|in:part1,part2,part3,part4,part5',
            'score'                  => 'required|integer|min:1|max:100',
            'difficulty'             => 'required|in:easy,medium,hard',
            'content'                => 'required|in:algebra,advanced_math,problem_solving_and_data_analysis,geometry_and_trigonometry',
            'explanation'            => 'nullable|string',
            'question_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'explanation_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'correct_answer'         => 'required_if:type,tf,numeric|nullable',
            'options'                => 'required_if:type,mcq|array|max:6',
            'options.*.option_text'  => 'nullable|string',
            'options.*.is_correct'   => 'nullable|boolean',
            'options.*.option_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        $submittedOptions = $request->all()['options'] ?? [];

        if ($request->type === 'mcq' && $request->has('options')) {
            $hasCorrectAnswer = false;
            foreach ($submittedOptions as $option) {
                if (isset($option['is_correct']) && $option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.must_select_correct_answer'),
                    'errors'  => ['options' => [__('l.must_select_correct_answer')]]
                ], 422);
            }
        }

        if ($optionCountErrors = $this->validateCompleteMcqOptions($submittedOptions, $request->type, $question)) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $optionCountErrors
            ], 422);
        }

        if ($duplicateErrors = $this->validateUniqueMcqOptions($submittedOptions, $request->type, $question)) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $duplicateErrors
            ], 422);
        }

        $this->keepOnlyUsableMcqOptionsForSaving($request, $question);

        $selectedPart = $request->part;
        $oldPart      = $question->part;

        $test = Test::find($question->test_id);

        if ($test) {
            $countField   = $selectedPart . '_questions_count';
            $maxQuestions = (int) ($test->$countField ?? 0);
            $currentCount = $test->questions()->where('part', $selectedPart)->count();

            if ($maxQuestions > 0) {
                if ($selectedPart !== $oldPart && $currentCount >= $maxQuestions) {
                    return response()->json([
                        'success' => false,
                        'message' => __('l.part_questions_complete'),
                        'errors'  => ['part' => [__('l.part_questions_complete')]]
                    ], 400);
                }
            }
        }

        try {
            DB::beginTransaction();

            $questionImagePath    = $question->question_image;
            $explanationImagePath = $question->explanation_image;

            if ((string) $request->input('remove_question_image') === '1' && $question->question_image) {
                delete_from_public($question->question_image);
                $questionImagePath = null;
            }

            if ((string) $request->input('remove_explanation_image') === '1' && $question->explanation_image) {
                delete_from_public($question->explanation_image);
                $explanationImagePath = null;
            }

            if ($request->hasFile('question_image') && (string) $request->input('remove_question_image') !== '1') {
                if ($question->question_image) {
                    delete_from_public($question->question_image);
                }
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            if ($request->hasFile('explanation_image') && (string) $request->input('remove_explanation_image') !== '1') {
                if ($question->explanation_image) {
                    delete_from_public($question->explanation_image);
                }
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question->update([
                'question_text'     => $request->question_text,
                'explanation'       => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image'    => $questionImagePath,
                'type'              => $request->type,
                'part'              => $selectedPart,
                'score'             => $request->score,
                'difficulty'        => $request->difficulty,
                'content'           => $request->content,
                'correct_answer'    => $request->type === 'mcq' ? '' : $request->correct_answer
            ]);

            if ($request->type === 'mcq' && $request->has('options')) {
                $existingOptions = $question->options()->get()->keyBy('id');
                $submittedOptionIds = [];

                foreach ($request->options as $index => $optionData) {
                    $optionId = isset($optionData['id']) && is_numeric($optionData['id'])
                        ? (int) $optionData['id']
                        : null;

                    if ($optionId && isset($existingOptions[$optionId])) {
                        $option = $existingOptions[$optionId];
                    } else {
                        $option = new TestQuestionOption();
                        $option->test_question_id = $question->id;
                    }

                    $option->option_text = $optionData['option_text'] ?? '';
                    $option->is_correct = isset($optionData['is_correct']) && ($optionData['is_correct'] == true || $optionData['is_correct'] == '1');
                    $option->option_order = $index + 1;

                    $removeByOptionsArray = isset($optionData['remove_image']) && (string) $optionData['remove_image'] === '1';

                    $removeByLegacyArray = false;
                    if ($optionId && $request->has('remove_option_image')) {
                        $legacyRemove = $request->input("remove_option_image.$optionId");
                        $removeByLegacyArray = (string) $legacyRemove === '1';
                    }

                    if ($removeByOptionsArray || $removeByLegacyArray) {
                        if ($option->option_image) {
                            delete_from_public($option->option_image);
                        }
                        $option->option_image = null;
                    }

                    if (isset($optionData['option_image']) && $optionData['option_image'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($option->option_image) {
                            delete_from_public($option->option_image);
                        }
                        $option->option_image = upload_to_public($optionData['option_image'], 'images/options');
                    }

                    $option->save();

                    if ($option->id) {
                        $submittedOptionIds[] = $option->id;
                    }
                }

                foreach ($existingOptions as $oldOptionId => $oldOption) {
                    if (!in_array($oldOptionId, $submittedOptionIds)) {
                        if ($oldOption->option_image) {
                            delete_from_public($oldOption->option_image);
                        }
                        $oldOption->delete();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('l.question_updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating test question: ' . $e->getMessage(), [
                'question_id'  => $question->id,
                'request_data' => $request->except(['question_image', 'explanation_image']),
                'trace'        => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview($id)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with(['course', 'questions.options'])->findOrFail(decrypt($id));

        $modulesQuestions = [];
        for ($i = 1; $i <= 5; $i++) {
            $modulesQuestions[$i] = $test->questions()
                ->where('part', 'part' . $i)
                ->with('options')
                ->get();
        }

        $part1Questions = $modulesQuestions[1];
        $part2Questions = $modulesQuestions[2];

        return view('themes.default.back.admins.tests.preview', compact(
            'test',
            'part1Questions',
            'part2Questions',
            'modulesQuestions'
        ));
    }

    private function validateCompleteMcqOptions(array $options, string $questionType, ?TestQuestion $question = null): ?array
    {
        if ($questionType !== 'mcq') {
            return null;
        }

        $existingOptions = $question
            ? $question->options->keyBy('id')
            : collect();

        $usableOptions = array_filter(
            $options,
            fn($optionData) => $this->mcqOptionHasUsableContent($optionData, $existingOptions)
        );

        if (count($usableOptions) !== 4) {
            return [
                'options' => ['Multiple choice questions must have 4 answer choices.']
            ];
        }

        return null;
    }

    private function validateUniqueMcqOptions(array $options, string $questionType, ?TestQuestion $question = null): ?array
    {
        if ($questionType !== 'mcq') {
            return null;
        }

        $existingOptions = $question
            ? $question->options->keyBy('id')
            : collect();

        $seen = [];

        foreach ($options as $optionData) {
            if (!$this->mcqOptionHasUsableContent($optionData, $existingOptions)) {
                continue;
            }

            $text = $this->normalizeOptionTextForDuplicateCheck($optionData['option_text'] ?? '');
            $imageIdentifier = $this->optionImageIdentifierForDuplicateCheck($optionData, $existingOptions);

            if ($imageIdentifier === null && $text === '') {
                continue;
            }

            $key = $imageIdentifier === null
                ? 'text:' . $text
                : 'text-image:' . $text . '|image:' . $imageIdentifier;

            if (isset($seen[$key])) {
                return [
                    'options' => ['Duplicate answer choices detected. Please make each option unique.']
                ];
            }

            $seen[$key] = true;
        }

        return null;
    }

    private function prepareStoreRequestForValidation(Request $request, Test $test): void
    {
        if ((int) $request->input('score', 0) < 1) {
            $score = $this->scoreForStoreRequest($request, $test);

            if ($score !== null) {
                $request->merge(['score' => $score]);
            }
        }
    }

    private function keepOnlyUsableMcqOptionsForSaving(Request $request, ?TestQuestion $question = null): void
    {
        if ($request->input('type') !== 'mcq') {
            return;
        }

        $options = $request->all()['options'] ?? null;

        if (!is_array($options)) {
            return;
        }

        $existingOptions = $question
            ? $question->options->keyBy('id')
            : collect();

        $request->merge([
            'options' => array_values(array_filter(
                $options,
                fn($option) => $this->mcqOptionHasUsableContent($option, $existingOptions)
            ))
        ]);
    }

    private function mcqOptionHasUsableContent(mixed $option, $existingOptions): bool
    {
        if (!is_array($option)) {
            return false;
        }

        if ($this->normalizeOptionTextForDuplicateCheck($option['option_text'] ?? '') !== '') {
            return true;
        }

        if (($option['option_image'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
            return true;
        }

        if (isset($option['id']) && is_numeric($option['id'])) {
            $existingOption = $existingOptions->get((int) $option['id']);

            return $existingOption
                && (string) ($option['remove_image'] ?? '0') !== '1'
                && filled($existingOption->option_image);
        }

        return false;
    }

    private function scoreForStoreRequest(Request $request, Test $test): ?int
    {
        $part = (string) $request->input('part', '');
        $difficulty = (string) $request->input('difficulty', '');

        if (!preg_match('/^part([1-5])$/', $part, $matches) || !in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            return null;
        }

        $field = 'module' . $matches[1] . '_' . $difficulty . '_score';
        $score = (int) ($test->$field ?? 0);

        return $score > 0 ? $score : null;
    }

    private function normalizeOptionTextForDuplicateCheck(mixed $value): string
    {
        return preg_replace('/\s+/u', ' ', trim((string) $value)) ?? '';
    }

    private function optionImageIdentifierForDuplicateCheck(array $optionData, $existingOptions): ?string
    {
        if (($optionData['option_image'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
            $hash = hash_file('sha256', $optionData['option_image']->getRealPath());

            return $hash ? 'upload:' . $hash : null;
        }

        if (isset($optionData['id']) && is_numeric($optionData['id'])) {
            $option = $existingOptions->get((int) $optionData['id']);

            if ($option && (string) ($optionData['remove_image'] ?? '0') !== '1' && filled($option->option_image)) {
                return 'existing:' . trim((string) $option->option_image);
            }
        }

        return null;
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $question = TestQuestion::with('options')->find($request->id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => __('l.question_not_found')
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($question->options as $option) {
                if ($option->option_image) {
                    delete_from_public($option->option_image);
                }
            }

            if ($question->question_image) {
                delete_from_public($question->question_image);
            }

            if ($question->explanation_image) {
                delete_from_public($question->explanation_image);
            }

            $question->options()->delete();
            $question->delete();

            $remainingQuestions = TestQuestion::where('test_id', $question->test_id)
                ->where('part', $question->part)
                ->orderBy('question_order')
                ->get();

            foreach ($remainingQuestions as $index => $q) {
                $q->update(['question_order' => $index + 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('l.question_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred')
            ], 500);
        }
    }

    private function getAvailableParts(Test $test)
    {
        $availableParts = [];

        $partsMeta = [
            'part1' => [
                'count_field' => 'part1_questions_count',
                'label'       => __('l.first_part'),
            ],
            'part2' => [
                'count_field' => 'part2_questions_count',
                'label'       => __('l.second_part'),
            ],
            'part3' => [
                'count_field' => 'part3_questions_count',
                'label'       => __('l.third_part'),
            ],
            'part4' => [
                'count_field' => 'part4_questions_count',
                'label'       => __('l.fourth_part'),
            ],
            'part5' => [
                'count_field' => 'part5_questions_count',
                'label'       => __('l.fifth_part'),
            ],
        ];

        foreach ($partsMeta as $partKey => $meta) {
            if (!isset($test->{$meta['count_field']})) {
                $max = 0;
            } else {
                $max = (int) $test->{$meta['count_field']};
            }

            $count = $test->questions()->where('part', $partKey)->count();

            if ($max <= 0) {
                $remaining = 0;
                $canAdd    = true;
            } else {
                $remaining = max($max - $count, 0);
                $canAdd    = ($max - $count) > 0;
            }

            $availableParts[$partKey] = [
                'label'     => $meta['label'],
                'current'   => $count,
                'max'       => $max,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        return $availableParts;
    }
}
