<?php

namespace App\Http\Controllers\Web\Back\Admins\Lectures;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Lecture;
use App\Models\Course;
use App\Models\Level;
use App\Models\LectureAssignment;
use App\Models\LectureQuestion;
use App\Models\LectureQuestionOption;
use Yajra\DataTables\Facades\DataTables;

class LecturesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $lectures = Lecture::with(['course.level'])->select(['id', 'name', 'course_id', 'description', 'video_url', 'type', 'price', 'created_at']);

            // تطبيق الفلاتر
            if ($request->filled('course_id')) {
                $lectures->where('course_id', $request->course_id);
            }

            if ($request->filled('level_id')) {
                $lectures->whereHas('course', function($query) use ($request) {
                    $query->where('level_id', $request->level_id);
                });
            }

            if ($request->filled('type')) {
                $lectures->where('type', $request->type);
            }

            if ($request->filled('date_from')) {
                $lectures->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $lectures->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($lectures)
                ->addIndexColumn()
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : '-';
                })
                ->addColumn('level_name', function($row) {
                    return $row->course && $row->course->level ? $row->course->level->name : '-';
                })
                ->addColumn('type', function($row) {
                    $types = [
                        'free' => __('l.Free'),
                        'price' => __('l.Paid'),
                        'month' => __('l.Monthly'),
                        'course' => __('l.Course')
                    ];
                    return $types[$row->type] ?? $row->type;
                })
                ->addColumn('price', function($row) {
                    if ($row->type === 'free') {
                        return __('l.Free');
                    }
                    if ($row->type === 'course') {
                        return '-';
                    }
                    if ($row->type === 'month') {
                        return $row->price ? $row->price . ' ' . __('l.Month') : '-';
                    }
                    return $row->price ? $row->price : '-';
                })
                ->addColumn('assignments_count', function($row) {
                    return $row->assignments->count();
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.lectures.action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $courses = Course::with('level')->get();
        $levels = Level::all();
        return view('themes/default/back.admins.lectures.lectures-list', compact('courses', 'levels'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'files' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar|max:10240',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:free,price,month,course',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle price based on type
        $price = null;
        if ($request->type === 'price' && $request->filled('price')) {
            $price = $request->price;
        }
        // المحاضرات الشهرية لا تحتاج سعر منفصل - السعر يكون من الاشتراك الشهري

        $imagePath = null;
        $filesPath = null;

        if ($request->hasFile('image')) {
            $imagePath = upload_to_public($request->file('image'), 'images/lectures');
        }

        if ($request->hasFile('files')) {
            $filesPath = upload_to_public($request->file('files'), 'files/lectures');
        }

        Lecture::create([
            'name' => $request->name,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'files' => $filesPath,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'price' => $price,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Lecture added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $lecture = Lecture::findOrFail(decrypt($request->id));
        $courses = Course::all();

        return view('themes/default/back.admins.lectures.lectures-edit', compact('lecture', 'courses'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'files' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar|max:10240',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:free,price,month,course',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle price based on type
        $price = null;
        if ($request->type === 'price' && $request->filled('price')) {
            $price = $request->price;
        }
        // المحاضرات الشهرية لا تحتاج سعر منفصل - السعر يكون من الاشتراك الشهري

        $lecture = Lecture::findOrFail(decrypt($request->id));

        $imagePath = $lecture->image;
        $filesPath = $lecture->files;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                delete_from_public($imagePath);
            }
            $imagePath = upload_to_public($request->file('image'), 'images/lectures');
        }

        if ($request->hasFile('files')) {
            if ($filesPath) {
                delete_from_public($filesPath);
            }
            $filesPath = upload_to_public($request->file('files'), 'files/lectures');
        }

        $lecture->update([
            'name' => $request->name,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'files' => $filesPath,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'price' => $price,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Lecture updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $lecture = Lecture::findOrFail(decrypt($request->id));

        if ($lecture->image) {
            delete_from_public($lecture->image);
        }

        if ($lecture->files) {
            delete_from_public($lecture->files);
        }

        $lecture->delete();

        return redirect()->back()->with('success', __('l.Lecture deleted successfully'));
    }

    // ==================== الواجبات ====================

    public function assignments(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $lecture = Lecture::with(['assignments.questions'])->findOrFail(decrypt($request->id));

        if ($request->ajax()) {
            $assignments = $lecture->assignments;

            return DataTables::of($assignments)
                ->addIndexColumn()
                ->addColumn('time_limit', function($row) {
                    return $row->time_limit ? $row->time_limit . ' ' . __('l.minutes') : __('l.No Limit');
                })
                ->addColumn('questions_count', function($row) {
                    return $row->questions->count();
                })
                ->addColumn('status', function($row) {
                    if (!$row->is_active) {
                        return '<span class="badge bg-danger">' . __('l.Inactive') . '</span>';
                    }
                    return '<span class="badge bg-success">' . __('l.Active') . '</span>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.lectures.assignments-action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('themes/default/back.admins.lectures.assignments', compact('lecture'));
    }

    public function storeAssignment(Request $request)
    {
        if (!Gate::allows('add lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lecture_id' => 'required',
            'time_limit' => 'nullable|integer|min:1',
            'show_answers' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // التحقق من وجود المحاضرة
        try {
            $lectureId = decrypt($request->lecture_id);
            $lecture = Lecture::findOrFail($lectureId);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['lecture_id' => 'The selected lecture id is invalid.'])->withInput();
        }

        // معالجة time_limit
        $timeLimit = null;
        if ($request->filled('time_limit') && is_numeric($request->time_limit) && $request->time_limit > 0) {
            $timeLimit = (int) $request->time_limit;
        }

        LectureAssignment::create([
            'title' => $request->title,
            'description' => $request->filled('description') ? $request->description : null,
            'lecture_id' => $lectureId,
            'time_limit' => $timeLimit,
            'show_answers' => $request->boolean('show_answers'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', __('l.Assignment added successfully'));
    }

    public function editAssignment(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $assignment = LectureAssignment::findOrFail(decrypt($request->id));
        $lecture = $assignment->lecture;

        return view('themes/default/back.admins.lectures.assignments-edit', compact('assignment', 'lecture'));
    }

    public function updateAssignment(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'show_answers' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $assignment = LectureAssignment::findOrFail(decrypt($request->id));

        // معالجة time_limit
        $timeLimit = null;
        if ($request->filled('time_limit') && is_numeric($request->time_limit) && $request->time_limit > 0) {
            $timeLimit = (int) $request->time_limit;
        }

        $assignment->update([
            'title' => $request->title,
            'description' => $request->filled('description') ? $request->description : null,
            'time_limit' => $timeLimit,
            'show_answers' => $request->boolean('show_answers'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', __('l.Assignment updated successfully'));
    }

    public function deleteAssignment(Request $request)
    {
        if (!Gate::allows('delete lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $assignment = LectureAssignment::findOrFail(decrypt($request->id));
        $assignment->delete();

        return redirect()->back()->with('success', __('l.Assignment deleted successfully'));
    }

    public function previewAssignment(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $assignment = LectureAssignment::with(['questions.options'])->findOrFail(decrypt($request->id));

        return view('themes/default/back.admins.lectures.assignment-preview', compact('assignment'));
    }

    // ==================== الأسئلة ====================

    public function questions(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $assignment = LectureAssignment::with(['questions.options'])->findOrFail(decrypt($request->id));

        if ($request->ajax()) {
            $questions = $assignment->questions;

            return DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('type', function($row) {
                    $types = [
                        'mcq' => __('l.Multiple Choice'),
                        'tf' => __('l.True/False'),
                        'essay' => __('l.Essay'),
                        'numeric' => __('l.Numeric')
                    ];
                    return $types[$row->type] ?? $row->type;
                })
                ->addColumn('options_count', function($row) {
                    return $row->options->count();
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.lectures.questions-action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // إضافة فحص للخيارات الصحيحة في MCQ
        foreach ($assignment->questions as $question) {
            if ($question->type === 'mcq') {
                $correctOptionsCount = $question->options->where('is_correct', true)->count();
                if ($correctOptionsCount === 0) {
                    logger('MCQ Question without correct answer found', [
                        'question_id' => $question->id,
                        'question_text' => $question->question_text,
                        'options' => $question->options->map(function($opt) {
                            return [
                                'id' => $opt->id,
                                'text' => $opt->option_text,
                                'is_correct' => $opt->is_correct
                            ];
                        })->toArray()
                    ]);
                }
            }
        }

        return view('themes/default/back.admins.lectures.questions', compact('assignment'));
    }

    public function storeQuestion(Request $request)
    {
        if (!Gate::allows('add lectures')) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:mcq,tf,essay,numeric',
            'lecture_assignment_id' => 'required|integer|exists:lecture_assignments,id',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'options' => 'required_if:type,mcq|array|min:2',
            'options.*.option_text' => 'required_if:type,mcq|string',
            'options.*.option_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        try {
            logger('StoreQuestion: Request data', $request->all());
            logger('StoreQuestion: Files', $request->files->all());

            $questionImagePath = null;
            if ($request->hasFile('question_image')) {
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            $explanationImagePath = null;
            if ($request->hasFile('explanation_image')) {
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question = LectureQuestion::create([
                'question_text' => $request->question_text,
                'type' => $request->type,
                'lecture_assignment_id' => $request->lecture_assignment_id, // استقبال الـ ID مباشرة
                'points' => $request->points,
                'correct_answer' => $request->correct_answer,
                'explanation' => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image' => $questionImagePath,
                'order' => LectureQuestion::where('lecture_assignment_id', $request->lecture_assignment_id)->count() + 1,
            ]);

            // إضافة الخيارات للاختيار من متعدد
            if ($request->type === 'mcq' && $request->has('options')) {
                logger('Creating MCQ options', [
                    'question_id' => $question->id,
                    'options_received' => $request->options
                ]);

                foreach ($request->options as $index => $option) {
                    $optionImagePath = null;
                    if (isset($option['option_image']) && $option['option_image']) {
                        $optionImagePath = upload_to_public($option['option_image'], 'images/options');
                    }

                    // التأكد من تحويل is_correct بشكل صحيح
                    $isCorrect = false;
                    if (isset($option['is_correct'])) {
                        $isCorrect = in_array($option['is_correct'], [1, '1', true, 'true', 'on'], true);
                    }

                    $createdOption = LectureQuestionOption::create([
                        'lecture_question_id' => $question->id,
                        'option_text' => $option['option_text'],
                        'option_image' => $optionImagePath,
                        'is_correct' => $isCorrect,
                        'order' => $index + 1,
                    ]);

                    logger('Created MCQ option', [
                        'option_id' => $createdOption->id,
                        'option_text' => $option['option_text'],
                        'is_correct_received' => $option['is_correct'] ?? 'not_set',
                        'is_correct_saved' => $isCorrect
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('l.question_saved_successfully'),
                    'question_id' => $question->id
                ]);
            }

            return redirect()->back()->with('success', __('l.Question added successfully'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => __('l.error_saving_question')], 500);
            }
            return redirect()->back()->with('error', __('l.error_saving_question'));
        }
    }

    public function updateQuestion(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:lecture_questions,id',
            'question_text' => 'required|string',
            'type' => 'required|in:mcq,tf,essay,numeric',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'options' => 'required_if:type,mcq|array|min:2',
            'options.*.option_text' => 'required_if:type,mcq|string',
            'options.*.option_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        try {
            logger('UpdateQuestion: Request data', $request->all());
            logger('UpdateQuestion: Files', $request->files->all());
            logger('UpdateQuestion: Received ID = ' . $request->id);
            $question = LectureQuestion::findOrFail($request->id); // استقبال الـ ID مباشرة
            logger('UpdateQuestion: Found question = ' . $question->id);

            $questionImagePath = $question->question_image;
            if ($request->hasFile('question_image')) {
                if ($questionImagePath && file_exists(public_path($questionImagePath))) {
                    delete_from_public($questionImagePath);
                }
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            $explanationImagePath = $question->explanation_image;
            if ($request->hasFile('explanation_image')) {
                if ($explanationImagePath && file_exists(public_path($explanationImagePath))) {
                    delete_from_public($explanationImagePath);
                }
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question->update([
                'question_text' => $request->question_text,
                'type' => $request->type,
                'points' => $request->points,
                'correct_answer' => $request->correct_answer,
                'explanation' => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image' => $questionImagePath,
            ]);

            // تحديث الخيارات للاختيار من متعدد
            if ($request->type === 'mcq' && $request->has('options')) {
                $existingOptions = $question->options->keyBy('id');
                $existingOptionsByOrder = $question->options->sortBy('order')->values();
                $optionsToKeep = [];

                foreach ($request->options as $index => $optionData) {
                    $optionId = $optionData['id'] ?? null;
                    $optionText = $optionData['option_text'] ?? '';
                    $isCorrect = isset($optionData['is_correct']) ? in_array($optionData['is_correct'], [1, '1', true, 'true', 'on'], true) : false;
                    $optionImagePath = null;

                    $option = null;
                    if ($optionId && $existingOptions->has($optionId)) {
                        $option = $existingOptions->get($optionId);
                        $optionsToKeep[] = $optionId;
                    } else {
                        // مطابقة احتياطية حسب الترتيب عندما لا تُرسل الـ ID
                        $fallbackOption = $existingOptionsByOrder->get($index);
                        if ($fallbackOption) {
                            $option = $fallbackOption;
                            $optionsToKeep[] = $fallbackOption->id;
                        }
                    }

                    $optionImagePath = null;
                    if ($option) { // If it's an existing option
                        $optionImagePath = $option->option_image; // Start with the existing image path
                    }

                    // Check if a new image file is uploaded for this option
                    if ($request->hasFile("options.{$index}.option_image")) {
                        // If a new image is uploaded, delete the old one if it exists
                        if ($option && $option->option_image && file_exists(public_path($option->option_image))) {
                            delete_from_public($option->option_image);
                        }
                        $optionImagePath = upload_to_public($request->file("options.{$index}.option_image"), 'images/options');
                    } else {
                        // No new image file uploaded.
                        // Check if the option data explicitly indicates removal of the image.
                        // This assumes the frontend sends 'option_image' as null or an empty string
                        // when the user intends to remove the image.
                        if (isset($optionData['option_image']) && (is_null($optionData['option_image']) || $optionData['option_image'] === '')) {
                            // User explicitly removed the image. Delete the old one if it exists.
                            if ($option && $option->option_image && file_exists(public_path($option->option_image))) {
                                delete_from_public($option->option_image);
                            }
                            $optionImagePath = null; // Set to null as it's removed
                        }
                        // If no new image uploaded and no explicit removal, $optionImagePath retains its value from the existing option.
                    }

                    if ($option) {
                        // Update existing option
                        $option->update([
                            'option_text' => $optionText,
                            'option_image' => $optionImagePath,
                            'is_correct' => $isCorrect,
                            'order' => $index + 1,
                        ]);
                    } else {
                        // Create new option
                        LectureQuestionOption::create([
                            'lecture_question_id' => $question->id,
                            'option_text' => $optionText,
                            'option_image' => $optionImagePath,
                            'is_correct' => $isCorrect,
                            'order' => $index + 1,
                        ]);
                    }
                }

                // Delete options that were not in the request (i.e., removed by the user)
                $optionsToDelete = $existingOptions->keys()->diff($optionsToKeep)->all();
                foreach (LectureQuestionOption::whereIn('id', $optionsToDelete)->get() as $option) {
                    if ($option->option_image && file_exists(public_path($option->option_image))) {
                        delete_from_public($option->option_image);
                    }
                    $option->delete();
                }

                logger('Updating MCQ options', [
                    'question_id' => $question->id,
                    'options_received' => $request->options
                ]);
            } else {
                // إذا لم يكن السؤال من نوع MCQ، احذف جميع الخيارات
                foreach ($question->options as $option) {
                    if ($option->option_image && file_exists(public_path($option->option_image))) {
                        delete_from_public($option->option_image);
                    }
                }
                $question->options()->delete();
            } 

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('l.question_saved_successfully')
                ]);
            }

            return redirect()->back()->with('success', __('l.Question updated successfully'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => __('l.error_saving_question')], 500);
            }
            return redirect()->back()->with('error', __('l.error_saving_question'));
        }
    }

    public function deleteQuestion(Request $request)
    {
        if (!Gate::allows('delete lectures')) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:lecture_questions,id'
        ]);

        try {
            logger('DeleteQuestion: Received ID = ' . $request->id);
            $question = LectureQuestion::findOrFail($request->id); // استقبال الـ ID مباشرة
            logger('DeleteQuestion: Found question = ' . $question->id);

            // حذف صورة السؤال
            if ($question->question_image && file_exists(public_path($question->question_image))) {
                delete_from_public($question->question_image);
            }

            // حذف صور الخيارات
            foreach ($question->options as $option) {
                if ($option->option_image && file_exists(public_path($option->option_image))) {
                    delete_from_public($option->option_image);
                }
            }

            $question->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('l.question_deleted_successfully')
                ]);
            }

            return redirect()->back()->with('success', __('l.Question deleted successfully'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => __('l.error_deleting_question')], 500);
            }
            return redirect()->back()->with('error', __('l.error_deleting_question'));
        }
    }
}