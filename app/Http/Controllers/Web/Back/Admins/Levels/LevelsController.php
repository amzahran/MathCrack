<?php

namespace App\Http\Controllers\Web\Back\Admins\Levels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Level;
use App\Models\User;
use App\Models\Course;
use Yajra\DataTables\Facades\DataTables;

class LevelsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show levels')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $levels = Level::withCount(['students', 'courses']);

            return DataTables::of($levels)
                ->addIndexColumn()
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.levels.action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('themes/default/back.admins.levels.levels-list');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add levels')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Level::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', __('l.Level added successfully'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit levels')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $level = Level::findOrFail(decrypt($request->id));
        $level->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', __('l.Level updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete levels')) {
            return view('themes/default/back.permission-denied');
        }

        $level = Level::findOrFail(decrypt($request->id));

        // التحقق من عدم وجود مستخدمين مرتبطين بهذا المستوى
        if ($level->students()->count() > 0) {
            return redirect()->back()->with('error', __('l.Cannot delete level. There are users assigned to this level.'));
        }

        $level->delete();

        return redirect()->back()->with('success', __('l.Level deleted successfully'));
    }

    public function students(Request $request)
    {
        if (!Gate::allows('show levels')) {
            return view('themes/default/back.permission-denied');
        }

        $level = null;
        if ($request->level_id) {
            $level = Level::find($request->level_id);
        }

        if ($request->ajax()) {
            $students = User::where('level_id', $request->level_id)
                           ->select(['id', 'firstname', 'lastname', 'email', 'phone', 'created_at']);

            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('name', function($row) {
                    return $row->firstname . ' ' . $row->lastname;
                })
                ->addColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.levels.students-action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('themes/default/back.admins.levels.levels-students', compact('level'));
    }

    public function courses(Request $request)
    {
        if (!Gate::allows('show levels')) {
            return view('themes/default/back.permission-denied');
        }

        $level = null;
        if ($request->level_id) {
            $level = Level::find($request->level_id);
        }

        if ($request->ajax()) {
            $courses = Course::where('level_id', $request->level_id)
                            ->select(['id', 'name', 'price', 'image', 'created_at']);

            return DataTables::of($courses)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    return $row->image ? '<img src="' . asset($row->image) . '" alt="course" width="40" height="40" class="rounded" />' : '-';
                })
                ->addColumn('price', function($row) {
                    return $row->price ?? '-';
                })
                ->addColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.levels.courses-action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action','image'])
                ->make(true);
        }

        return view('themes/default/back.admins.levels.levels-courses', compact('level'));
    }
}
