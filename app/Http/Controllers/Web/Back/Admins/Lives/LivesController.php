<?php

namespace App\Http\Controllers\Web\Back\Admins\Lives;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Models\Live;
use App\Models\Course;
use App\Models\Level;
use Yajra\DataTables\Facades\DataTables;

class LivesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show lives')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            try {
                $lives = Live::with(['course.level'])->select(['id', 'name', 'course_id', 'description', 'link', 'start_at', 'duration', 'type', 'price', 'created_at']);

                // تطبيق الفلاتر
                if ($request->filled('course_id')) {
                    $lives->where('course_id', $request->course_id);
                }

                if ($request->filled('level_id')) {
                    $lives->whereHas('course', function($query) use ($request) {
                        $query->where('level_id', $request->level_id);
                    });
                }

                if ($request->filled('type')) {
                    $lives->where('type', $request->type);
                }

                if ($request->filled('date_from')) {
                    $lives->whereDate('created_at', '>=', $request->date_from);
                }

                if ($request->filled('date_to')) {
                    $lives->whereDate('created_at', '<=', $request->date_to);
                }

                return DataTables::of($lives)
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
                    ->addColumn('start_at', function($row) {
                        return $row->start_at ? $row->start_at->format('Y-m-d H:i') : '-';
                    })
                    ->addColumn('duration', function($row) {
                        if (!$row->duration) {
                            return __('l.Not specified');
                        }
                        if ($row->duration < 60) {
                            return $row->duration . ' ' . __('l.minutes');
                        }
                        $hours = floor($row->duration / 60);
                        $minutes = $row->duration % 60;
                        if ($minutes == 0) {
                            return $hours . ' ' . __('l.hours');
                        }
                        return $hours . ' ' . __('l.hours') . ' ' . $minutes . ' ' . __('l.minutes');
                    })
                    ->addColumn('status', function($row) {
                        if (!$row->start_at) {
                            return '<span class="badge bg-secondary">' . __('l.Not scheduled') . '</span>';
                        }

                        $now = now();
                        $startTime = $row->start_at;
                        $endTime = $row->start_at->addMinutes($row->duration ?? 0);

                        if ($now->lt($startTime)) {
                            return '<span class="badge bg-info">' . __('l.Upcoming') . '</span>';
                        } elseif ($now->gte($startTime) && $now->lt($endTime)) {
                            return '<span class="badge bg-success">' . __('l.Live Now') . '</span>';
                        } else {
                            return '<span class="badge bg-secondary">' . __('l.Ended') . '</span>';
                        }
                    })
                    ->editColumn('created_at', function($row) {
                        return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                    })
                    ->addColumn('action', function($row) {
                        $actionBtn = view('themes.default.back.admins.lives.action-buttons', ['row' => $row])->render();
                        return $actionBtn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Error in LivesController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Error loading data'], 500);
            }
        }

        $courses = Course::with('level')->get();
        $levels = Level::all();
        return view('themes/default/back.admins.lives.lives-list', compact('courses', 'levels'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add lives')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'start_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = upload_to_public($request->file('image'), 'images/lives');
        }

        Live::create([
            'name' => $request->name,
            'description' => $request->description,
            'link' => $request->link,
            'start_at' => $request->start_at,
            'duration' => $request->duration,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'price' => $price,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Live session added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit lives')) {
            return view('themes/default/back.permission-denied');
        }

        $live = Live::findOrFail(decrypt($request->id));
        $courses = Course::all();

        return view('themes/default/back.admins.lives.lives-edit', compact('live', 'courses'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit lives')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'start_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
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

        $live = Live::findOrFail(decrypt($request->id));

        $imagePath = $live->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                delete_from_public($imagePath);
            }
            $imagePath = upload_to_public($request->file('image'), 'images/lives');
        }

        $live->update([
            'name' => $request->name,
            'description' => $request->description,
            'link' => $request->link,
            'start_at' => $request->start_at,
            'duration' => $request->duration,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'price' => $price,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('success', __('l.Live session updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete lives')) {
            return view('themes/default/back.permission-denied');
        }

        $live = Live::findOrFail(decrypt($request->id));

        if ($live->image) {
            delete_from_public($live->image);
        }

        $live->delete();

        return redirect()->back()->with('success', __('l.Live session deleted successfully'));
    }
}
