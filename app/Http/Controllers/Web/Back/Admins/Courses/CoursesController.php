<?php

namespace App\Http\Controllers\Web\Back\Admins\Courses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\Level;
use Yajra\DataTables\Facades\DataTables;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show courses')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $courses = Course::with('level')->select(['id', 'name', 'level_id', 'price', 'access_duration_days', 'image', 'created_at']);

            // تطبيق الفلاتر
            if ($request->filled('level_id')) {
                $courses->where('level_id', $request->level_id);
            }

            if ($request->filled('price_range')) {
                switch ($request->price_range) {
                    case 'free':
                        $courses->whereNull('price')->orWhere('price', 0);
                        break;
                    case 'paid':
                        $courses->where('price', '>', 0);
                        break;
                    case 'low':
                        $courses->whereBetween('price', [1, 100]);
                        break;
                    case 'medium':
                        $courses->whereBetween('price', [101, 500]);
                        break;
                    case 'high':
                        $courses->where('price', '>', 500);
                        break;
                }
            }

            if ($request->filled('date_from')) {
                $courses->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $courses->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($courses)
                ->addIndexColumn()
                ->addColumn('level_name', function($row) {
                    return $row->level ? $row->level->name : '-';
                })
                ->addColumn('price', function($row) {
                    if ($row->price == 0 || $row->price === null) {
                        return '<span class="badge bg-success">' . __('l.Free') . '</span>';
                    }
                    return '<span class="badge bg-primary">' . $row->price . ' ' . __('l.currency') . '</span>';
                })
                ->addColumn('access_duration', function($row) {
                    $course = Course::find($row->id);
                    if (!$course || !$course->hasAccessDurationLimit()) {
                        return '<span class="badge bg-warning">' . __('l.Unlimited') . '</span>';
                    }
                    return '<span class="badge bg-info">' . $course->getFormattedAccessDuration() . '</span>';
                })
                ->addColumn('image', function($row) {
                    return $row->image ? '<img src="' . asset($row->image) . '" alt="course" width="40" height="40" class="rounded" />' : '-';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.courses.action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action','image','price','access_duration'])
                ->make(true);
        }

        $levels = Level::all();
        return view('themes/default/back.admins.courses.courses-list', compact('levels'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'price' => 'nullable|numeric',
            'tests_price' => 'nullable|numeric',
            'access_duration_days' => 'nullable|integer|min:0|max:3650',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = upload_to_public($request->file('image'), 'images/courses');
        }

        Course::create([
            'name' => $request->name,
            'level_id' => $request->level_id,
            'price' => $request->price,
            'tests_price' => $request->tests_price,
            'access_duration_days' => $request->access_duration_days ?? 90,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Course added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit courses')) {
            return view('themes/default/back.permission-denied');
        }

        $course = Course::findOrFail(decrypt($request->id));
        $levels = Level::all();

        return view('themes/default/back.admins.courses.courses-edit', compact('course', 'levels'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'price' => 'nullable|numeric',
            'tests_price' => 'nullable|numeric',
            'access_duration_days' => 'nullable|integer|min:0|max:3650',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $course = Course::findOrFail(decrypt($request->id));

        $imagePath = $course->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                delete_from_public($imagePath);
            }
            $imagePath = upload_to_public($request->file('image'), 'images/courses');
        }

        $course->update([
            'name' => $request->name,
            'level_id' => $request->level_id,
            'price' => $request->price,
            'tests_price' => $request->tests_price,
            'access_duration_days' => $request->access_duration_days ?? 90,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Course updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete courses')) {
            return view('themes/default/back.permission-denied');
        }

        $course = Course::findOrFail(decrypt($request->id));

        // التحقق من عدم وجود محاضرات مرتبطة بهذا الكورس
        // if ($course->lectures()->count() > 0) {
        //     return redirect()->back()->with('error', __('l.Cannot delete course. There are lectures assigned to this course.'));
        // }

        // حذف صورة الكورس إن وُجدت
        if ($course->image) {
            delete_from_public($course->image);
        }

        $course->delete();

        return redirect()->back()->with('success', __('l.Course deleted successfully'));
    }
}
