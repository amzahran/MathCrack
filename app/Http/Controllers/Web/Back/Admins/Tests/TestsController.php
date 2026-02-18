<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Test;
use App\Models\Course;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class TestsController extends Controller
{
    /*========================
     *  INDEX (LIST PAGE)
     *========================*/
    public function index(Request $request)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        // طلب AJAX للـ DataTables
        if ($request->ajax()) {
            $tests = Test::with(['course'])
                ->select([
                    'id',
                    'name',
                    'description',
                    'course_id',
                    'price',
                    'total_score',
                    'initial_score',
                    'is_active',
                    'created_at',
                    'part1_questions_count',
                    'part2_questions_count',
                    'part3_questions_count',
                    'part4_questions_count',
                    'part5_questions_count',
                ])
                ->orderBy('id', 'asc');

            // فلاتر
            if ($request->filled('course_id')) {
                $tests->where('course_id', $request->course_id);
            }

            if ($request->filled('status') && $request->status !== '') {
                $tests->where('is_active', $request->status);
            }

            if ($request->filled('price_type')) {
                if ($request->price_type === 'free') {
                    $tests->where('price', 0);
                } elseif ($request->price_type === 'paid') {
                    $tests->where('price', '>', 0);
                }
            }

            return DataTables::of($tests)
                ->addIndexColumn()
                ->addColumn('course_name', function ($test) {
                    return $test->course->name ?? '';
                })
                ->addColumn('price_formatted', function ($test) {
                    if ($test->price > 0) {
                        return number_format($test->price, 2) . ' ' . __('l.currency');
                    }
                    return '<span class="text-muted">' . __('l.free') . '</span>';
                })
                ->addColumn('questions_status', function ($test) {
                    $totalQuestions = $test->questions()->count();

                    $expectedQuestions =
                        (int) $test->part1_questions_count +
                        (int) $test->part2_questions_count +
                        (int) $test->part3_questions_count +
                        (int) $test->part4_questions_count +
                        (int) $test->part5_questions_count;

                    if ($expectedQuestions <= 0) {
                        return '<span class="badge bg-secondary">' . __('l.no_structure_defined') . '</span>';
                    }

                    if ($totalQuestions >= $expectedQuestions) {
                        return '<span class="badge bg-success">' . __('l.complete') . '</span>';
                    }

                    return '<span class="badge bg-warning">' . __('l.incomplete') . '</span> (' .
                        $totalQuestions . '/' . $expectedQuestions . ')';
                })
                ->addColumn('students_count', function ($test) {
                    return $test->studentTests()->count();
                })
                ->addColumn('status', function ($test) {
                    $checked = $test->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch">
                                <input class="form-check-input status-toggle" type="checkbox"
                                       data-id="' . $test->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($test) {
                    $buttons = '<div class="btn-group" role="group">';

                    // عرض
                    $buttons .= '<a href="' . route('dashboard.admins.tests-show', ['id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-info" title="' . __('l.View') . '">
                                   <i class="fas fa-eye"></i>
                                </a>';

                    // الأسئلة
                    $buttons .= '<a href="' . route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-primary" title="' . __('l.questions') . '">
                                   <i class="fas fa-question-circle"></i>
                                </a>';

                    // تعديل
                    $buttons .= '<a href="' . route('dashboard.admins.tests-edit', ['id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-warning" title="' . __('l.Edit') . '">
                                   <i class="fas fa-edit"></i>
                                </a>';

                    // داخل addColumn('action', function ($test) {

$buttons .= '<a href="' . route('dashboard.admins.tests-results-print', ['id' => encrypt($test->id)]) . '"
               class="btn btn-sm btn-secondary" target="_blank" title="Print Results">
               <i class="fas fa-print"></i>
            </a>';
            




                    // حذف (لو مفيش طلاب حلّوا الاختبار)
                    if (!$test->studentTests()->exists()) {
                        $buttons .= '<a href="' . route('dashboard.admins.tests-delete', ['id' => encrypt($test->id)]) . '"
                                       class="btn btn-sm btn-danger delete-record" title="' . __('l.delete') . '">
                                       <i class="fas fa-trash"></i>
                                    </a>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['price_formatted', 'questions_status', 'status', 'action'])
                ->make(true);
        }

        // الطلب العادي
        $courses = Course::orderBy('name')->get();
        return view('themes.default.back.admins.tests.index', compact('courses'));
    }

    /*========================
     *  STORE
     *========================*/
    public function store(Request $request)
    {
        if (!Gate::allows('add tests')) {
            return redirect()->back()->with('error', __('l.permission_denied'));
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(function ($query) use ($request) {
                    return $query->where('course_id', $request->course_id);
                })
            ],
            'description'            => 'nullable|string',
            'course_id'              => 'required|exists:courses,id',
            'price'                  => 'required|numeric|min:0',
            'total_score'            => 'required|integer|min:1|max:1000',
            'initial_score'          => 'required|integer|min:0|max:800',
            'default_question_score' => 'required|integer|min:1|max:100',

            'part1_questions_count'  => 'required|integer|min:1|max:100',
            'part1_time_minutes'     => 'required|integer|min:1|max:300',

            'part2_questions_count'  => 'nullable|integer|min:0|max:100',
            'part2_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part3_questions_count'  => 'nullable|integer|min:0|max:100',
            'part3_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part4_questions_count'  => 'nullable|integer|min:0|max:100',
            'part4_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part5_questions_count'  => 'nullable|integer|min:0|max:100',
            'part5_time_minutes'     => 'nullable|integer|min:0|max:300',

            'break_time_minutes'     => 'nullable|integer|min:0|max:60',
            'max_attempts'           => 'required|integer|min:1|max:10',
            'is_active'              => 'nullable|boolean',
        ], [
            'name.unique'        => __('l.test_name_exists_in_course'),
            'course_id.required' => __('l.course_required'),
            'price.required'     => __('l.price_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('l.validation_error'));
        }

        try {
            $data = $validator->validated();

            foreach ([2, 3, 4, 5] as $i) {
                $data["part{$i}_questions_count"] = $data["part{$i}_questions_count"] ?? 0;
                $data["part{$i}_time_minutes"]    = $data["part{$i}_time_minutes"] ?? 0;
            }

            $data['break_time_minutes'] = $data['break_time_minutes'] ?? 0;
            $data['is_active']          = $request->has('is_active') ? 1 : 0;

            Test::create($data);

            return redirect()->back()->with('success', __('l.test_created_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('l.error_occurred'))
                ->withInput();
        }
    }

    /*========================
     *  EDIT
     *========================*/
    public function edit(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->id));
        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        $courses = Course::orderBy('name')->get();
        return view('themes.default.back.admins.tests.edit', compact('test', 'courses'));
    }

    /*========================
     *  UPDATE
     *========================*/
    // ========================
// UPDATE  Clean version
// ========================
public function update(Request $request)
{
    if (!Gate::allows('edit tests')) {
        return redirect()->back()->with('error', __('l.permission_denied'));
    }

    $test = Test::find(decrypt($request->id));
    if (!$test) {
        return redirect()->back()->with('error', __('l.test_not_found'));
    }

    $validator = Validator::make($request->all(), [
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('tests')->where(function ($query) use ($request) {
                return $query->where('course_id', $request->course_id);
            })->ignore($test->id)
        ],
        'description'            => 'nullable|string',
        'course_id'              => 'required|exists:courses,id',
        'price'                  => 'required|numeric|min:0',

        'total_score'            => 'required|integer|min:1|max:1000',
        'initial_score'          => 'required|integer|min:0|max:800',
        'default_question_score' => 'required|integer|min:1|max:100',

        'part1_questions_count'  => 'required|integer|min:1|max:100',
        'part1_time_minutes'     => 'required|integer|min:1|max:300',

        'part2_questions_count'  => 'nullable|integer|min:0|max:100',
        'part2_time_minutes'     => 'nullable|integer|min:0|max:300',

        'part3_questions_count'  => 'nullable|integer|min:0|max:100',
        'part3_time_minutes'     => 'nullable|integer|min:0|max:300',

        'part4_questions_count'  => 'nullable|integer|min:0|max:100',
        'part4_time_minutes'     => 'nullable|integer|min:0|max:300',

        'part5_questions_count'  => 'nullable|integer|min:0|max:100',
        'part5_time_minutes'     => 'nullable|integer|min:0|max:300',

        'break_time_minutes'     => 'nullable|integer|min:0|max:60',
        'max_attempts'           => 'required|integer|min:1|max:10',
        'is_active'              => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', __('l.validation_error'));
    }

    try {
        $data = $validator->validated();

        foreach ([2, 3, 4, 5] as $i) {
            $data["part{$i}_questions_count"] = $data["part{$i}_questions_count"] ?? 0;
            $data["part{$i}_time_minutes"]    = $data["part{$i}_time_minutes"] ?? 0;
        }

        $data['break_time_minutes'] = $data['break_time_minutes'] ?? 0;
        $data['is_active']          = $request->has('is_active') ? 1 : 0;

        $test->update($data);

        return redirect()->back()->with('success', __('l.test_updated_successfully'));
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', __('l.error_occurred'))
            ->withInput();
    }
}


    /*========================
     *  SHOW
     *========================*/
    public function show(Request $request)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with(['course', 'questions.options', 'studentTests'])
    ->find(decrypt($request->id));

        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        $moduleStats   = [];
        $expectedTotal = 0;
        $totalTime     = 0;

        for ($i = 1; $i <= 5; $i++) {
            $partKey = 'part' . $i;

            // عمود part في جدول الأسئلة قيمته part1 / part2 / ...
            $questionsCount = $test->questions()
                ->where('part', $partKey)
                ->count();

            $expected     = (int) $test->{"part{$i}_questions_count"};
            $timeForPart  = (int) $test->{"part{$i}_time_minutes"};
            $expectedTotal += $expected;
            $totalTime     += $timeForPart;

            $moduleStats[$i] = [
                'questions' => $questionsCount,
                'expected'  => $expected,
                'complete'  => $expected > 0 ? $questionsCount >= $expected : true,
                'time'      => $timeForPart,
            ];
        }

        $totalQuestions = array_sum(array_column($moduleStats, 'questions'));

        $stats = [
            'total_questions'    => $totalQuestions,
            'expected_questions' => $expectedTotal,
            'part1_questions'    => $moduleStats[1]['questions'],
            'part2_questions'    => $moduleStats[2]['questions'],
            'part3_questions'    => $moduleStats[3]['questions'],
            'part4_questions'    => $moduleStats[4]['questions'],
            'part5_questions'    => $moduleStats[5]['questions'],
            'total_students'     => $test->studentTests()->count(),
            'completed_students' => $test->completedStudentTests()->count(),
            'average_score'      => $test->completedStudentTests()->avg('final_score') ?: 0,
            'highest_score'      => $test->completedStudentTests()->max('final_score') ?: 0,
            'lowest_score'       => $test->completedStudentTests()->min('final_score') ?: 0,
            'total_time'         => $totalTime,
            'break_time'         => (int) $test->break_time_minutes,
        ];

        $questionStatus = [
            'part1_complete' => $moduleStats[1]['complete'],
            'part2_complete' => $moduleStats[2]['complete'],
            'part3_complete' => $moduleStats[3]['complete'],
            'part4_complete' => $moduleStats[4]['complete'],
            'part5_complete' => $moduleStats[5]['complete'],
            'all_complete'   => $expectedTotal > 0 ? ($totalQuestions >= $expectedTotal) : true,
        ];

        return view('themes.default.back.admins.tests.show', compact(
            'test',
            'stats',
            'questionStatus',
            'moduleStats'
        ));
    }

    /*========================
     *  DELETE
     *========================*/
    public function delete(Request $request)
    {
        if (!Gate::allows('delete tests')) {
            return redirect()->back()->with('error', __('l.permission_denied'));
        }

        $test = Test::find(decrypt($request->id));
        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        if ($test->studentTests()->exists()) {
            return redirect()->back()
                ->with('error', __('l.cannot_delete_test_with_students'));
        }

        try {
            $testName = $test->name;
            $test->delete();

            return redirect()->back()->with('success', __('l.test_deleted_successfully', ['name' => $testName]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('l.error_occurred'));
        }
    }

    /*========================
     *  TOGGLE STATUS (AJAX)
     *========================*/
    public function toggleStatus(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return response()->json(['success' => false, 'message' => __('l.permission_denied')]);
        }

        $test = Test::find($request->id);
        if (!$test) {
            return response()->json(['success' => false, 'message' => __('l.test_not_found')]);
        }

        try {
            $test->update(['is_active' => !$test->is_active]);

            $message = $test->is_active ? __('l.test_activated') : __('l.test_deactivated');

            return response()->json([
                'success'    => true,
                'message'    => $message,
                'new_status' => $test->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('l.error_occurred')]);
        }
    }

    /*========================
     *  PREVIEW (ADMIN VIEW)
     *========================*/
    /*========================
 *  PREVIEW (ADMIN VIEW)
 *========================*/
public function preview($id)
{
    if (!Gate::allows('show tests')) {
        return view('themes/default/back.permission-denied');
    }

    try {
        $testId = decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }

    $test = Test::with(['course', 'questions.options'])
        ->findOrFail($testId);

    $modulesQuestions    = [];
    $totalQuestionsAdded = 0;
    $partsStats          = [];
    $totalTime           = 0;

    for ($partNumber = 1; $partNumber <= 5; $partNumber++) {

        $partKey = 'part' . $partNumber;

        $questions = $test->questions()
            ->where('part', $partKey)
            ->orderBy('question_order')
            ->get();

        $modulesQuestions[$partNumber] = $questions;
        $totalQuestionsAdded += $questions->count();

        $countField  = $partKey . '_questions_count';
        $timeField   = $partKey . '_time_minutes';

        $expected    = (int) $test->$countField;
        $timeMinutes = (int) $test->$timeField;
        $pointsSum   = $questions->sum('score');

        $partsStats[$partNumber] = [
            'expected_count' => $expected,
            'time_minutes'   => $timeMinutes,
            'points_sum'     => $pointsSum,
        ];

        $totalTime += $timeMinutes;
    }

    return view('themes.default.back.admins.tests.preview', compact(
        'test',
        'modulesQuestions',
        'totalQuestionsAdded',
        'partsStats',
        'totalTime'
    ));
}


public function printResults($id)
{
    if (!Gate::allows('show tests')) {
        return view('themes/default/back.permission-denied');
    }

    $testId = decrypt($id);

    $lastAttemptsSql = DB::table('student_tests')
        ->select('student_id', 'test_id', DB::raw('MAX(attempt_number) AS max_attempt'))
        ->where('test_id', $testId)
        ->groupBy('student_id', 'test_id');

    $rows = DB::table('student_tests as st')
        ->joinSub($lastAttemptsSql, 'last_attempts', function ($join) {
            $join->on('last_attempts.student_id', '=', 'st.student_id')
                ->on('last_attempts.test_id', '=', 'st.test_id')
                ->on('last_attempts.max_attempt', '=', 'st.attempt_number');
        })
        ->join('users as u', 'u.id', '=', 'st.student_id')
        ->join('tests as t', 't.id', '=', 'st.test_id')
        ->join('courses as c', 'c.id', '=', 't.course_id')

        // correct / wrong from student_test_answers
        ->leftJoin('student_test_answers as sta', 'sta.student_test_id', '=', 'st.id')

        ->where('st.test_id', $testId)
        ->select([
            DB::raw("CONCAT(u.firstname,' ',u.lastname) as student_name"),
            'u.email',
            'c.name as course_name',
            't.name as test_name',
            'st.attempt_number as last_attempt',
            'st.status',
            'st.final_score',
            't.total_score as test_total_score',
            'st.started_at',

            DB::raw('SUM(CASE WHEN sta.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
            DB::raw('SUM(CASE WHEN sta.is_correct = 0 THEN 1 ELSE 0 END) as wrong_answers'),
        ])
        ->groupBy(
            'u.id',
            'u.firstname',
            'u.lastname',
            'u.email',
            'c.name',
            't.name',
            'st.attempt_number',
            'st.status',
            'st.final_score',
            't.total_score',
            'st.started_at'
        )
        ->orderBy('student_name')
        ->get();

    return view('themes.default.back.admins.tests.print', compact('rows'));
}

}
