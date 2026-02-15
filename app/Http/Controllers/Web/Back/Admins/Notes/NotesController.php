<?php

namespace App\Http\Controllers\Web\Back\Admins\Notes;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotesController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $notes = Note::query()->where('user_id', auth()->id());

            return DataTables::of($notes)
                ->addIndexColumn()
                ->addColumn('action', function ($note) {
                    return '
                        <a href="' . route('dashboard.admins.notes-edit', ['id' => encrypt($note->id)]) . '"
                            class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="' . __('l.Edit') . '">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm delete-note"
                            data-id="' . encrypt($note->id) . '" data-bs-toggle="tooltip" title="' . __('l.Delete') . '">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->editColumn('note', function ($note) {
                    return Str::limit($note->note, 75);
                })
                ->editColumn('date', function ($note) {
                    return Carbon::parse($note->date)->format('Y-m-d');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('themes.default.back.admins.notes.notes-list');
    }

    public function show()
    {
        $notes = Note::where('user_id', auth()->user()->id)
        ->where(function ($query) {
            $query->where(function ($q) {
                $q->where('is_still_active', 1)
                    ->where('date', '<=', Carbon::now()->format('Y-m-d'));
            })
            ->orWhere(function ($q) {
                $q->where('is_still_active', 0)
                    ->where('date', Carbon::now()->format('Y-m-d'));
            });
        })
        ->orderBy('date', 'asc')
        ->get();

        return view('themes.default.back.admins.notes.notes-show', ['notes' => $notes]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'note' => 'required|string',
            'date' => 'required|date',
            'is_still_active' => 'required|boolean',
        ]);

        $note = new Note();
        $note->note = $request->input('note');
        $note->date = $request->input('date');
        $note->user_id = auth()->user()->id;
        $note->is_still_active = $request->input('is_still_active');
        $note->save();

        return redirect()->back()->with('success', __('l.Note added successfully'));
    }

    public function edit(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $note = Note::findOrFail($id);

        return view('themes.default.back.admins.notes.notes-edit', ['note' => $note]);
    }

    public function update(Request $request)
    {

        $request->validate([
            'note' => 'required|string',
            'date' => 'required|date',
            'is_still_active' => 'required|boolean',
        ]);

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $note = Note::findOrFail($id);

        $note->note = $request->input('note');
        $note->date = $request->input('date');
        $note->is_still_active = $request->input('is_still_active');
        $note->save();

        return redirect()->back()->with('success', __('l.Note updated successfully'));
    }

    public function delete(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $note = Note::findOrFail($id);

        $note->delete();

        return redirect()->back()->with('success', __('l.Note deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        Note::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', __('l.Notes deleted successfully'));
    }

    public function check()
    {
        try {
            if (request()->ajax()) {
                $notesCount = Note::where('user_id', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('is_still_active', 1)
                                ->where('date', '<=', Carbon::now()->format('Y-m-d'));
                        })
                        ->orWhere(function ($q) {
                            $q->where('is_still_active', 0)
                                ->where('date', Carbon::now()->format('Y-m-d'));
                        });
                    })
                    ->count();

                return response()->json([
                    'status' => 'success',
                    'notesCount' => $notesCount
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
